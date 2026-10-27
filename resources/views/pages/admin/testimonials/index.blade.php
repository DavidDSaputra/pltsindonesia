<?php

use App\Models\Media;
use App\Models\Testimonial;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

new #[Title('Testimoni')] class extends Component {
    use WithFileUploads;

    public ?int $testimonialId = null;

    public string $name = '';

    public string $role = '';

    public string $company = '';

    public string $quote = '';

    public string $rating = '5';

    public string $avatar = '';

    /** @var TemporaryUploadedFile|null */
    public $avatarUpload = null;

    public bool $is_active = true;

    public string $sort_order = '0';

    public ?int $confirmingId = null;

    /**
     * @return array<string, mixed>
     */
    protected function rulesFor(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'role' => ['nullable', 'string', 'max:120'],
            'company' => ['nullable', 'string', 'max:120'],
            'quote' => ['required', 'string', 'max:1200'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'avatar' => ['nullable', 'string', 'max:500'],
            'avatarUpload' => ['nullable', 'image', 'max:4096'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function validationAttributes(): array
    {
        return [
            'name' => 'nama pelanggan',
            'role' => 'jabatan',
            'company' => 'perusahaan',
            'quote' => 'isi testimoni',
            'rating' => 'penilaian',
            'avatar' => 'foto',
            'avatarUpload' => 'berkas foto',
            'sort_order' => 'urutan',
        ];
    }

    #[Computed]
    public function testimonials()
    {
        return Testimonial::query()->ordered()->get();
    }

    #[Computed]
    public function averageRating(): float
    {
        return (float) Testimonial::query()->active()->avg('rating');
    }

    public function updatedAvatarUpload(): void
    {
        $this->validate(['avatarUpload' => ['nullable', 'image', 'max:4096']]);
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

    public function create(): void
    {
        $this->resetForm();
        $this->sort_order = (string) ((int) Testimonial::query()->max('sort_order') + 10);
        $this->dispatch('modal-show', name: 'testimoni');
    }

    public function edit(int $testimonialId): void
    {
        $testimonial = Testimonial::query()->findOrFail($testimonialId);

        $this->testimonialId = $testimonial->id;
        $this->name = (string) $testimonial->name;
        $this->role = (string) $testimonial->role;
        $this->company = (string) $testimonial->company;
        $this->quote = (string) $testimonial->quote;
        $this->rating = (string) $testimonial->rating;
        $this->avatar = (string) $testimonial->avatar;
        $this->is_active = (bool) $testimonial->is_active;
        $this->sort_order = (string) $testimonial->sort_order;

        $this->avatarUpload = null;
        $this->resetValidation();

        $this->dispatch('modal-show', name: 'testimoni');
    }

    public function save(): void
    {
        $data = $this->validate($this->rulesFor(), attributes: $this->validationAttributes());

        $avatar = $data['avatar'] ?? '';

        if ($this->avatarUpload instanceof TemporaryUploadedFile) {
            $avatar = Media::storeUpload($this->avatarUpload, Auth::id(), 'testimonials')->path;
        }

        unset($data['avatarUpload']);

        $data['avatar'] = filled($avatar) ? $avatar : null;
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['rating'] = (int) $data['rating'];
        $data['is_active'] = $this->is_active;

        $testimonial = $this->testimonialId
            ? Testimonial::query()->findOrFail($this->testimonialId)
            : new Testimonial;

        $testimonial->fill($data);
        $testimonial->save();

        Flux::toast(
            variant: 'success',
            text: $testimonial->wasRecentlyCreated ? 'Testimoni ditambahkan.' : 'Testimoni diperbarui.',
        );

        $this->dispatch('modal-close', name: 'testimoni');
        $this->resetForm();
    }

    public function toggleActive(int $testimonialId): void
    {
        $testimonial = Testimonial::query()->findOrFail($testimonialId);

        $testimonial->update(['is_active' => ! $testimonial->is_active]);

        Flux::toast(
            variant: 'success',
            text: $testimonial->is_active ? 'Testimoni ditayangkan.' : 'Testimoni disembunyikan.',
        );
    }

    public function confirmDelete(int $testimonialId): void
    {
        $this->confirmingId = $testimonialId;

        $this->dispatch('modal-show', name: 'hapus-testimoni');
    }

    public function destroy(): void
    {
        Testimonial::query()->findOrFail($this->confirmingId)->delete();

        Flux::toast(variant: 'success', text: 'Testimoni dihapus.');

        $this->confirmingId = null;
        $this->dispatch('modal-close', name: 'hapus-testimoni');
    }

    #[Computed]
    public function confirmingName(): string
    {
        return $this->confirmingId
            ? (string) Testimonial::query()->whereKey($this->confirmingId)->value('name')
            : '';
    }

    protected function resetForm(): void
    {
        $this->reset([
            'testimonialId', 'name', 'role', 'company', 'quote', 'avatar', 'sort_order', 'confirmingId',
        ]);

        $this->rating = '5';
        $this->is_active = true;
        $this->avatarUpload = null;
        $this->resetValidation();
    }
}; ?>

<div class="space-y-6">
    <x-admin.page-header
        title="Testimoni"
        :description="'Ulasan pelanggan yang tampil di beranda. Rata-rata penilaian aktif: '.($this->averageRating > 0 ? number_format($this->averageRating, 1, ',', '.') : '—').' dari 5.'"
    >
        <flux:button variant="primary" icon="plus" type="button" wire:click="create">
            Tambah Testimoni
        </flux:button>
    </x-admin.page-header>

    @if ($this->testimonials->isEmpty())
        <x-admin.empty-state
            title="Belum ada testimoni"
            description="Minta ulasan dari pelanggan yang sudah merasakan penghematan tagihan listriknya."
            icon="star"
        >
            <flux:button variant="primary" icon="plus" type="button" wire:click="create">
                Tambah Testimoni
            </flux:button>
        </x-admin.empty-state>
    @else
        <ul class="grid gap-4 lg:grid-cols-2 xl:grid-cols-3" role="list">
            @foreach ($this->testimonials as $testimonial)
                <li
                    wire:key="testimoni-{{ $testimonial->id }}"
                    class="flex flex-col gap-3 rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-800"
                >
                    <div class="flex items-start gap-3">
                        <flux:avatar :src="$testimonial->avatar_url" :name="$testimonial->name" />

                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium text-zinc-800 dark:text-zinc-100">
                                {{ $testimonial->name }}
                            </p>

                            <flux:text size="sm" class="truncate">
                                {{ $testimonial->attribution ?: 'Pelanggan' }}
                            </flux:text>
                        </div>

                        <flux:badge :color="$testimonial->is_active ? 'green' : 'zinc'" size="sm" inset="left right">
                            {{ $testimonial->is_active ? 'Tayang' : 'Sembunyi' }}
                        </flux:badge>
                    </div>

                    <p
                        class="flex items-center gap-1"
                        role="img"
                        aria-label="Penilaian {{ $testimonial->rating }} dari 5 bintang"
                    >
                        @for ($star = 1; $star <= 5; $star++)
                            <flux:icon
                                name="star"
                                :variant="$star <= $testimonial->rating ? 'solid' : 'outline'"
                                :class="['size-4', $star <= $testimonial->rating ? 'text-amber-500' : 'text-zinc-300 dark:text-zinc-600']"
                            />
                        @endfor
                    </p>

                    <flux:text size="sm" class="line-clamp-4 flex-1">“{{ $testimonial->quote }}”</flux:text>

                    <div class="flex flex-wrap gap-1 border-t border-zinc-200 pt-3 dark:border-zinc-700">
                        <flux:button size="xs" variant="ghost" icon="pencil-square" type="button" wire:click="edit({{ $testimonial->id }})">
                            Ubah
                        </flux:button>

                        <flux:button size="xs" variant="ghost" icon="eye" type="button" wire:click="toggleActive({{ $testimonial->id }})">
                            {{ $testimonial->is_active ? 'Sembunyikan' : 'Tayangkan' }}
                        </flux:button>

                        <flux:button
                            size="xs"
                            variant="ghost"
                            icon="trash"
                            type="button"
                            class="text-red-500"
                            wire:click="confirmDelete({{ $testimonial->id }})"
                        >
                            Hapus
                        </flux:button>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif

    <flux:modal name="testimoni" scroll="body" class="min-w-[24rem] max-w-2xl space-y-5">
        <form wire:submit="save" class="space-y-5">
            <div class="space-y-1">
                <flux:heading level="2" size="lg">{{ $testimonialId ? 'Ubah Testimoni' : 'Tambah Testimoni' }}</flux:heading>

                <flux:text size="sm">Pastikan izin tayang sudah diperoleh dari pelanggan yang bersangkutan.</flux:text>
            </div>

            <div class="grid gap-4 sm:grid-cols-3">
                <flux:input wire:model.blur="name" label="Nama" type="text" required placeholder="Budi Santoso" />

                <flux:input wire:model.blur="role" label="Jabatan" type="text" placeholder="Pemilik Rumah" />

                <flux:input wire:model.blur="company" label="Perusahaan / kota" type="text" placeholder="Tangerang Selatan" />
            </div>

            <flux:textarea
                wire:model.blur="quote"
                label="Isi testimoni"
                rows="4"
                required
                placeholder="Tagihan listrik turun hampir separuh sejak pasang PLTS atap. Pemasangannya rapi dan timnya menjelaskan dengan sabar."
            />

            <div class="grid gap-4 sm:grid-cols-3">
                <flux:select wire:model.blur="rating" label="Penilaian">
                    @foreach ([5, 4, 3, 2, 1] as $star)
                        <flux:select.option value="{{ $star }}">{{ $star }} bintang</flux:select.option>
                    @endforeach
                </flux:select>

                <flux:input
                    wire:model.blur="sort_order"
                    label="Urutan"
                    description="Angka kecil tampil lebih dulu."
                    type="number"
                    min="0"
                    step="1"
                />

                <div class="space-y-1">
                    <flux:label>Foto</flux:label>

                    @if ($this->avatarPreview)
                        <img src="{{ $this->avatarPreview }}" alt="" class="size-12 rounded-full object-cover">
                    @else
                        <flux:avatar :name="$name ?: 'PLTS'" />
                    @endif
                </div>
            </div>

            <x-admin.image-field
                label="Unggah foto pelanggan"
                model="avatarUpload"
                path-model="avatar"
                :preview="$this->avatarPreview"
                description="Foto kotak 1:1 berukuran kecil sudah cukup."
            />

            <flux:switch wire:model="is_active" label="Tayangkan testimoni ini di situs" />

            <div class="flex gap-2 border-t border-zinc-200 pt-4 dark:border-zinc-700">
                <flux:spacer />

                <flux:modal.close>
                    <flux:button variant="ghost" type="button">Batal</flux:button>
                </flux:modal.close>

                <flux:button variant="primary" type="submit" icon="check" wire:loading.attr="disabled">
                    Simpan
                </flux:button>
            </div>
        </form>
    </flux:modal>

    <x-admin.confirm-delete name="hapus-testimoni" title="Hapus testimoni?">
        Testimoni dari “{{ $this->confirmingName }}” akan dihapus dan tidak lagi dihitung dalam rata-rata penilaian.
    </x-admin.confirm-delete>
</div>
