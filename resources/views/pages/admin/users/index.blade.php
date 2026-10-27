<?php

use App\Enums\UserRole;
use App\Models\Media;
use App\Models\User;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

new #[Title('Pengguna')] class extends Component {
    use WithFileUploads, WithPagination;

    #[Url(as: 'cari')]
    public string $search = '';

    #[Url(as: 'peran')]
    public string $roleFilter = '';

    public ?int $userId = null;

    public string $name = '';

    public string $email = '';

    public string $role = UserRole::Editor->value;

    public string $bio = '';

    public string $password = '';

    public string $password_confirmation = '';

    public bool $isVerified = true;

    public string $avatar = '';

    public $avatarUpload = null;

    public ?int $confirmingId = null;

    public function updating($property): void
    {
        if (in_array($property, ['search', 'roleFilter'], true)) {
            $this->resetPage();
        }
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'roleFilter']);
        $this->resetPage();
    }

    #[Computed]
    public function users()
    {
        return User::query()
            ->withCount('articles')
            ->when($this->roleFilter !== '', fn ($query) => $query->where('role', $this->roleFilter))
            ->when(filled($this->search), function ($query) {
                $like = '%'.str_replace(['%', '_'], ['\%', '\_'], $this->search).'%';

                $query->where(function ($inner) use ($like) {
                    $inner->where('name', 'like', $like)
                        ->orWhere('email', 'like', $like);
                });
            })
            ->orderByRaw("CASE role WHEN 'admin' THEN 0 WHEN 'editor' THEN 1 ELSE 2 END")
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();
    }

    #[Computed]
    public function counts(): array
    {
        return [
            'all' => User::query()->count(),
            'admin' => User::query()->where('role', UserRole::Admin)->count(),
            'editor' => User::query()->where('role', UserRole::Editor)->count(),
            'unverified' => User::query()->whereNull('email_verified_at')->count(),
        ];
    }

    /**
     * @return array<string, string>
     */
    #[Computed]
    public function roleOptions(): array
    {
        return UserRole::options();
    }

    public function create(): void
    {
        $this->resetForm();

        $this->dispatch('modal-show', name: 'pengguna');
    }

    public function edit(int $userId): void
    {
        $user = User::query()->findOrFail($userId);

        $this->resetForm();

        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role->value;
        $this->bio = (string) $user->bio;
        $this->avatar = (string) $user->avatar;
        $this->isVerified = $user->email_verified_at !== null;

        $this->resetValidation();

        $this->dispatch('modal-show', name: 'pengguna');
    }

    protected function resetForm(): void
    {
        $this->reset([
            'userId', 'name', 'email', 'bio', 'password',
            'password_confirmation', 'avatar', 'avatarUpload',
        ]);

        $this->role = UserRole::Editor->value;
        $this->isVerified = true;

        $this->resetValidation();
    }

    public function updatedAvatarUpload(): void
    {
        if (! $this->avatarUpload instanceof TemporaryUploadedFile) {
            return;
        }

        $this->validate(
            ['avatarUpload' => ['image', 'max:4096']],
            ['avatarUpload.image' => 'Berkas harus berupa gambar.', 'avatarUpload.max' => 'Ukuran gambar maksimal 4 MB.'],
            ['avatarUpload' => 'Foto profil'],
        );

        $this->avatar = Media::storeUpload($this->avatarUpload, Auth::id(), 'avatars')->path;

        Flux::toast(variant: 'success', text: 'Foto profil diunggah. Jangan lupa menyimpan.');
    }

    #[Computed]
    public function avatarPreview(): ?string
    {
        if ($this->avatarUpload instanceof TemporaryUploadedFile) {
            try {
                return $this->avatarUpload->temporaryUrl();
            } catch (\Throwable) {
                return null;
            }
        }

        return image_url($this->avatar ?: null);
    }

    /**
     * @return array<string, mixed>
     */
    protected function rulesFor(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => [
                'required', 'string', 'lowercase', 'email:filter', 'max:160',
                Rule::unique('users', 'email')->ignore($this->userId),
            ],
            'role' => ['required', Rule::enum(UserRole::class)],
            'bio' => ['nullable', 'string', 'max:400'],
            'avatar' => ['nullable', 'string', 'max:2048'],
            'password' => [$this->userId ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email ini sudah dipakai pengguna lain.',
            'role.required' => 'Peran wajib dipilih.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'avatarUpload.image' => 'Berkas harus berupa gambar.',
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function validationAttributes(): array
    {
        return [
            'name' => 'nama',
            'email' => 'email',
            'role' => 'peran',
            'bio' => 'biografi singkat',
            'avatar' => 'foto profil',
            'password' => 'kata sandi',
        ];
    }

    public function save(): void
    {
        $data = $this->validate($this->rulesFor(), $this->messages(), $this->validationAttributes());

        $user = $this->userId ? User::query()->findOrFail($this->userId) : new User;

        $newRole = UserRole::from($data['role']);

        if ($user->exists && $user->role === UserRole::Admin && $newRole !== UserRole::Admin) {
            $this->guardLastAdmin($user->id, 'role');
        }

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->role = $newRole;
        $user->bio = filled($data['bio']) ? $data['bio'] : null;
        $user->avatar = filled($data['avatar']) ? $data['avatar'] : null;

        if (filled($data['password'])) {
            $user->password = $data['password'];
        }

        $user->email_verified_at = $this->isVerified
            ? ($user->email_verified_at ?? now())
            : null;

        $user->save();

        $wasCreated = $user->wasRecentlyCreated;

        $this->resetForm();

        $this->dispatch('modal-close', name: 'pengguna');

        Flux::toast(
            variant: 'success',
            text: $wasCreated ? 'Pengguna baru dibuat.' : 'Data pengguna disimpan.',
        );
    }

    /**
     * Refuse to strip the only remaining administrator.
     */
    protected function guardLastAdmin(int $userId, string $field): void
    {
        $others = User::query()
            ->where('role', UserRole::Admin)
            ->whereKeyNot($userId)
            ->exists();

        if ($others) {
            return;
        }

        throw ValidationException::withMessages([
            $field => 'Administrator terakhir tidak boleh diturunkan atau dihapus. Promosikan pengguna lain lebih dulu.',
        ]);
    }

    public function confirmDelete(int $userId): void
    {
        if ($userId === Auth::id()) {
            Flux::toast(variant: 'danger', text: 'Anda tidak bisa menghapus akun sendiri.');

            return;
        }

        $this->guardLastAdmin($userId, 'search');

        $this->confirmingId = $userId;

        $this->dispatch('modal-show', name: 'hapus-pengguna');
    }

    public function destroy(): void
    {
        $user = User::query()->findOrFail($this->confirmingId);

        if ($user->id === Auth::id()) {
            Flux::toast(variant: 'danger', text: 'Anda tidak bisa menghapus akun sendiri.');

            return;
        }

        $user->delete();

        Flux::toast(variant: 'success', text: $user->name.' dihapus.');

        $this->confirmingId = null;
        $this->dispatch('modal-close', name: 'hapus-pengguna');
    }

    #[Computed]
    public function confirmingName(): string
    {
        return $this->confirmingId
            ? (string) (User::query()->whereKey($this->confirmingId)->value('name') ?? '')
            : '';
    }

    #[Computed]
    public function isSelf(): bool
    {
        return $this->userId !== null && $this->userId === Auth::id();
    }
}; ?>

<div class="space-y-6">
    <x-admin.page-header
        title="Pengguna"
        description="Kelola siapa saja yang boleh masuk ke CMS. Administrator mengatur semuanya, Editor hanya mengelola konten."
        :href="null"
    >
        <flux:button icon="user-plus" type="button" variant="primary" wire:click="create">
            Tambah Pengguna
        </flux:button>
    </x-admin.page-header>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-admin.stat-card label="Total pengguna" :value="$this->counts['all']" icon="users" hint="semua akun" />
        <x-admin.stat-card label="Administrator" :value="$this->counts['admin']" icon="shield-check" hint="akses penuh" />
        <x-admin.stat-card label="Editor" :value="$this->counts['editor']" icon="pencil-square" hint="kelola konten" />
        <x-admin.stat-card label="Belum verifikasi" :value="$this->counts['unverified']" icon="envelope" hint="email belum dikonfirmasi" />
    </div>

    <flux:card class="space-y-4">
        <div class="grid gap-4 md:grid-cols-3">
            <flux:input
                wire:model.live.debounce.400ms="search"
                label="Cari"
                icon="magnifying-glass"
                placeholder="Nama atau email…"
                type="search"
                class="md:col-span-2"
            />

            <flux:select wire:model.live="roleFilter" label="Peran" placeholder="Semua peran">
                @foreach (UserRole::cases() as $option)
                    <flux:select.option value="{{ $option->value }}">{{ $option->label() }}</flux:select.option>
                @endforeach
            </flux:select>
        </div>

        @if ($search !== '' || $roleFilter !== '')
            <flux:button size="sm" variant="ghost" icon="x-mark" type="button" wire:click="resetFilters">
                Bersihkan filter
            </flux:button>
        @endif
    </flux:card>

    @if ($this->users->isEmpty())
        <x-admin.empty-state
            title="Tidak ada pengguna"
            description="Belum ada akun yang cocok dengan pencarian Anda."
            icon="users"
        />
    @else
        <flux:card class="overflow-hidden">
            <div class="-mx-4 overflow-x-auto sm:mx-0">
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>Pengguna</flux:table.column>
                        <flux:table.column align="center">Peran</flux:table.column>
                        <flux:table.column class="hidden lg:table-cell" align="center">Artikel</flux:table.column>
                        <flux:table.column class="hidden md:table-cell">Bergabung</flux:table.column>
                        <flux:table.column align="end">Aksi</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @foreach ($this->users as $user)
                            <flux:table.row wire:key="pengguna-{{ $user->id }}">
                                <flux:table.cell>
                                    <div class="flex min-w-0 items-center gap-3">
                                        <flux:avatar :src="$user->avatar_url" :name="$user->name" size="sm" />

                                        <div class="min-w-0">
                                            <p class="max-w-56 truncate font-medium text-zinc-800 dark:text-zinc-100">
                                                {{ $user->name }}

                                                @if ($user->id === Auth::id())
                                                    <span class="text-xs font-normal text-zinc-500 dark:text-zinc-400">(Anda)</span>
                                                @endif
                                            </p>

                                            <flux:text size="sm" class="max-w-56 truncate">{{ $user->email }}</flux:text>

                                            @if ($user->email_verified_at === null)
                                                <flux:badge color="amber" size="sm" inset="left right" class="mt-1">
                                                    Email belum diverifikasi
                                                </flux:badge>
                                            @endif
                                        </div>
                                    </div>
                                </flux:table.cell>

                                <flux:table.cell align="center">
                                    <flux:badge :color="$user->isAdmin() ? 'blue' : null" inset="left right">
                                        {{ $user->role->label() }}
                                    </flux:badge>
                                </flux:table.cell>

                                <flux:table.cell class="hidden lg:table-cell" align="center">
                                    <span>{{ $user->articles_count }}</span>
                                </flux:table.cell>

                                <flux:table.cell class="hidden md:table-cell">
                                    <span>{{ $user->created_at?->translatedFormat('j M Y') ?? '—' }}</span>
                                </flux:table.cell>

                                <flux:table.cell align="end">
                                    <div class="flex justify-end gap-1">
                                        <flux:button size="xs" variant="ghost" icon="pencil-square" type="button" wire:click="edit({{ $user->id }})">
                                            <span class="sr-only">Ubah {{ $user->name }}</span>
                                        </flux:button>

                                        <flux:button
                                            size="xs"
                                            variant="ghost"
                                            icon="trash"
                                            type="button"
                                            class="text-red-500"
                                            :disabled="$user->id === Auth::id()"
                                            wire:click="confirmDelete({{ $user->id }})"
                                        >
                                            <span class="sr-only">Hapus {{ $user->name }}</span>
                                        </flux:button>
                                    </div>
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            </div>

            <flux:pagination :paginator="$this->users" class="mt-4" />
        </flux:card>
    @endif

    <flux:modal name="pengguna" scroll="body" class="min-w-[24rem] max-w-2xl space-y-5">
        <div class="space-y-1">
            <flux:heading level="2" size="lg">
                {{ $userId ? 'Ubah Pengguna' : 'Tambah Pengguna' }}
            </flux:heading>

            <flux:text size="sm">
                {{ $userId ? 'Perbarui identitas, peran, dan kata sandi akun ini.' : 'Buat akun baru untuk tim redaksi atau administrator situs.' }}
            </flux:text>
        </div>

        <form wire:submit="save" class="space-y-5">
            <div class="grid gap-4 sm:grid-cols-2">
                <flux:input wire:model.blur="name" label="Nama lengkap" type="text" required placeholder="Rina Kartika" />

                <flux:input wire:model.blur="email" label="Email" type="email" required placeholder="nama@pltsindonesia.com" />
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <flux:select wire:model="role" label="Peran" description="Editor tidak bisa membuka halaman Pengguna dan Pengaturan.">
                    @foreach ($this->roleOptions as $value => $label)
                        <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                    @endforeach
                </flux:select>

                <flux:input
                    wire:model.blur="bio"
                    label="Biografi singkat"
                    type="text"
                    placeholder="Penulis energi terbarukan"
                    description="Muncul sebagai nama penulis pada artikel."
                />
            </div>

            <x-admin.image-field
                label="Foto profil"
                model="avatarUpload"
                path-model="avatar"
                :preview="$this->avatarPreview"
            />

            <div class="grid gap-4 sm:grid-cols-2">
                <flux:input
                    wire:model="password"
                    :label="$userId ? 'Kata sandi baru (kosongkan bila tidak diganti)' : 'Kata sandi'"
                    type="password"
                    autocomplete="new-password"
                    :required="! $userId"
                    placeholder="Minimal 8 karakter"
                />

                <flux:input
                    wire:model="password_confirmation"
                    label="Ulangi kata sandi"
                    type="password"
                    autocomplete="new-password"
                    :required="! $userId"
                    placeholder="Ketik ulang kata sandi"
                />
            </div>

            <flux:switch
                wire:model="isVerified"
                label="Email sudah terverifikasi"
                description="Matikan bila pengguna harus mengklik tautan verifikasi yang dikirim ke emailnya."
            />

            @if ($this->isSelf)
                <flux:callout color="amber" icon="exclamation-triangle">
                    Anda sedang mengubah akun sendiri. Menurunkan peran Anda akan membuat Anda kehilangan akses ke halaman ini.
                </flux:callout>
            @endif

            <flux:error name="role" />

            <div class="flex gap-2 border-t border-zinc-200 pt-4 dark:border-zinc-700">
                <flux:spacer />

                <flux:modal.close>
                    <flux:button variant="ghost" type="button">Batal</flux:button>
                </flux:modal.close>

                <flux:button variant="primary" type="submit" icon="check" wire:loading.attr="disabled">
                    {{ $userId ? 'Simpan Perubahan' : 'Buat Pengguna' }}
                </flux:button>
            </div>
        </form>
    </flux:modal>

    <x-admin.confirm-delete name="hapus-pengguna" title="Hapus pengguna?">
        Akun “{{ $this->confirmingName }}” akan dihapus. Artikel yang sudah ditulis tetap ada, hanya penulisnya dilepas.
    </x-admin.confirm-delete>
</div>
