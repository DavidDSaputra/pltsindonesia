<?php

use App\Models\Gallery;
use App\Models\Media;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

new #[Title('Galeri')] class extends Component {
    use WithFileUploads, WithPagination;

    #[Url(as: 'grup')]
    public string $groupFilter = '';

    public ?int $galleryId = null;

    public string $title = '';

    public string $image = '';

    /** @var TemporaryUploadedFile|null */
    public $imageUpload = null;

    public string $alt = '';

    public string $group = '';

    public bool $is_active = true;

    public string $sort_order = '0';

    public ?int $confirmingId = null;

    /**
     * @return array<string, mixed>
     */
    protected function rulesFor(): array
    {
        return [
            'title' => ['required', 'string', 'max:160'],
            'image' => ['required_without:imageUpload', 'nullable', 'string', 'max:500'],
            'imageUpload' => ['required_without:image', 'nullable', 'image', 'max:4096'],
            'alt' => ['nullable', 'string', 'max:220'],
            'group' => ['nullable', 'string', 'max:80'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function validationAttributes(): array
    {
        return [
            'title' => 'judul foto',
            'image' => 'gambar',
            'imageUpload' => 'berkas gambar',
            'alt' => 'teks alt',
            'group' => 'grup',
            'sort_order' => 'urutan',
        ];
    }

    public function updatingGroupFilter(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function images()
    {
        return Gallery::query()
            ->when($this->groupFilter !== '', fn ($query) => $query->where('group', $this->groupFilter))
            ->ordered()
            ->paginate(24)
            ->withQueryString();
    }

    /**
     * @return array<int, string>
     */
    #[Computed]
    public function groups(): array
    {
        return Gallery::query()
            ->whereNotNull('group')
            ->where('group', '!=', '')
            ->distinct()
            ->orderBy('group')
            ->pluck('group')
            ->all();
    }

    public function updatedImageUpload(): void
    {
        $this->validate(['imageUpload' => ['nullable', 'image', 'max:4096']]);
    }

    /**
     * Fill the alt text from the title so no image is left without one.
     */
    public function updatedTitle(): void
    {
        if ($this->alt === '') {
            $this->alt = $this->title;
        }
    }

    #[Computed]
    public function imagePreview(): ?string
    {
        if ($this->imageUpload instanceof TemporaryUploadedFile) {
            try {
                return $this->imageUpload->temporaryUrl();
            } catch (\Throwable) {
                return null;
            }
        }

        return image_url($this->image ?: null);
    }

    public function create(): void
    {
        $this->resetForm();
        $this->sort_order = (string) ((int) Gallery::query()->max('sort_order') + 10);
        $this->group = $this->groupFilter;

        $this->dispatch('modal-show', name: 'galeri');
    }

    public function edit(int $galleryId): void
    {
        $gallery = Gallery::query()->findOrFail($galleryId);

        $this->galleryId = $gallery->id;
        $this->title = (string) $gallery->title;
        $this->image = (string) $gallery->image;
        $this->alt = (string) $gallery->alt;
        $this->group = (string) $gallery->group;
        $this->is_active = (bool) $gallery->is_active;
        $this->sort_order = (string) $gallery->sort_order;

        $this->imageUpload = null;
        $this->resetValidation();

        $this->dispatch('modal-show', name: 'galeri');
    }

    public function save(): void
    {
        $data = $this->validate($this->rulesFor(), attributes: $this->validationAttributes());

        $image = $data['image'] ?? '';

        if ($this->imageUpload instanceof TemporaryUploadedFile) {
            $image = Media::storeUpload($this->imageUpload, Auth::id(), 'galleries')->path;
        }

        unset($data['imageUpload']);

        $data['image'] = filled($image) ? $image : null;
        $data['alt'] = filled($data['alt']) ? $data['alt'] : $data['title'];
        $data['group'] = filled($data['group']) ? $data['group'] : null;
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['is_active'] = $this->is_active;

        $gallery = $this->galleryId ? Gallery::query()->findOrFail($this->galleryId) : new Gallery;

        $gallery->fill($data);
        $gallery->save();

        Flux::toast(
            variant: 'success',
            text: $gallery->wasRecentlyCreated ? 'Foto ditambahkan ke galeri.' : 'Foto diperbarui.',
        );

        $this->dispatch('modal-close', name: 'galeri');
        $this->resetForm();
    }

    public function toggleActive(int $galleryId): void
    {
        $gallery = Gallery::query()->findOrFail($galleryId);

        $gallery->update(['is_active' => ! $gallery->is_active]);

        Flux::toast(
            variant: 'success',
            text: $gallery->is_active ? 'Foto ditayangkan.' : 'Foto disembunyikan.',
        );
    }

    public function confirmDelete(int $galleryId): void
    {
        $this->confirmingId = $galleryId;

        $this->dispatch('modal-show', name: 'hapus-galeri');
    }

    public function destroy(): void
    {
        Gallery::query()->findOrFail($this->confirmingId)->delete();

        Flux::toast(variant: 'success', text: 'Foto dihapus dari galeri.');

        $this->confirmingId = null;
        $this->dispatch('modal-close', name: 'hapus-galeri');
    }

    #[Computed]
    public function confirmingTitle(): string
    {
        return $this->confirmingId
            ? (string) Gallery::query()->whereKey($this->confirmingId)->value('title')
            : '';
    }

    protected function resetForm(): void
    {
        $this->reset(['galleryId', 'title', 'image', 'alt', 'group', 'sort_order', 'confirmingId']);

        $this->is_active = true;
        $this->imageUpload = null;
        $this->resetValidation();
    }
}; ?>

<div class="space-y-6">
    <x-admin.page-header
        title="Galeri"
        description="Dokumentasi pemasangan yang tampil di halaman /galeri. Teks alt wajib diisi agar ramah pembaca layar."
    >
        <flux:button variant="primary" icon="plus" type="button" wire:click="create">
            Tambah Foto
        </flux:button>
    </x-admin.page-header>

    @if ($this->groups !== [])
        <flux:card class="flex flex-wrap items-center gap-2">
            <flux:text size="sm" class="me-1">Filter grup:</flux:text>

            <flux:button
                size="xs"
                :variant="$groupFilter === '' ? 'primary' : 'outline'"
                type="button"
                wire:click="$set('groupFilter', '')"
                :aria-pressed="$groupFilter === '' ? 'true' : 'false'"
            >
                Semua
            </flux:button>

            @foreach ($this->groups as $group)
                <flux:button
                    size="xs"
                    :variant="$groupFilter === $group ? 'primary' : 'outline'"
                    type="button"
                    wire:click="$set('groupFilter', @js($group))"
                    :aria-pressed="$groupFilter === $group ? 'true' : 'false'"
                >
                    {{ $group }}
                </flux:button>
            @endforeach
        </flux:card>
    @endif

    @if ($this->images->isEmpty())
        <x-admin.empty-state
            title="Galeri masih kosong"
            description="Unggah foto pemasangan, hasil akhir atap, dan tim yang sedang bekerja."
            icon="rectangle-group"
        >
            <flux:button variant="primary" icon="plus" type="button" wire:click="create">
                Tambah Foto
            </flux:button>
        </x-admin.empty-state>
    @else
        <div class="space-y-4">
            <ul class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4" role="list">
                @foreach ($this->images as $gallery)
                    <li
                        wire:key="galeri-{{ $gallery->id }}"
                        class="group overflow-hidden rounded-xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-800"
                    >
                        <div class="relative aspect-[4/3] bg-zinc-100 dark:bg-zinc-900">
                            @if ($gallery->image_url)
                                <img
                                    src="{{ $gallery->image_url }}"
                                    alt="{{ $gallery->alt_text }}"
                                    class="size-full object-cover"
                                    loading="lazy"
                                >
                            @else
                                <span class="grid size-full place-items-center text-zinc-400">
                                    <flux:icon name="photo" class="size-6" />
                                </span>
                            @endif

                            <div class="absolute inset-x-0 top-0 flex justify-end gap-1 p-2 opacity-100 transition sm:opacity-0 sm:group-focus-within:opacity-100 sm:group-hover:opacity-100">
                                <button
                                    type="button"
                                    wire:click="edit({{ $gallery->id }})"
                                    class="grid size-7 place-items-center rounded-md bg-white/90 text-zinc-700 shadow-sm hover:bg-white dark:bg-zinc-800/90 dark:text-zinc-200"
                                >
                                    <span class="sr-only">Ubah {{ $gallery->title }}</span>
                                    <flux:icon name="pencil-square" variant="mini" class="size-4" />
                                </button>

                                <button
                                    type="button"
                                    wire:click="toggleActive({{ $gallery->id }})"
                                    class="grid size-7 place-items-center rounded-md bg-white/90 text-zinc-700 shadow-sm hover:bg-white dark:bg-zinc-800/90 dark:text-zinc-200"
                                >
                                    <span class="sr-only">{{ $gallery->is_active ? 'Sembunyikan' : 'Tayangkan' }} {{ $gallery->title }}</span>
                                    <flux:icon :name="$gallery->is_active ? 'eye' : 'eye-slash'" variant="mini" class="size-4" />
                                </button>

                                <button
                                    type="button"
                                    wire:click="confirmDelete({{ $gallery->id }})"
                                    class="grid size-7 place-items-center rounded-md bg-white/90 text-red-500 shadow-sm hover:bg-white dark:bg-zinc-800/90"
                                >
                                    <span class="sr-only">Hapus {{ $gallery->title }}</span>
                                    <flux:icon name="trash" variant="mini" class="size-4" />
                                </button>
                            </div>

                            @unless ($gallery->is_active)
                                <span class="absolute bottom-2 start-2">
                                    <flux:badge color="zinc" size="sm" inset="left right">Sembunyi</flux:badge>
                                </span>
                            @endunless
                        </div>

                        <div class="space-y-0.5 p-3">
                            <p class="truncate text-sm font-medium text-zinc-800 dark:text-zinc-100">{{ $gallery->title }}</p>

                            <flux:text size="sm" class="truncate">
                                {{ $gallery->group ?: 'Tanpa grup' }} · urutan {{ $gallery->sort_order }}
                            </flux:text>
                        </div>
                    </li>
                @endforeach
            </ul>

            <flux:pagination :paginator="$this->images" />
        </div>
    @endif

    <flux:modal name="galeri" scroll="body" class="min-w-[24rem] max-w-2xl space-y-5">
        <form wire:submit="save" class="space-y-5">
            <div class="space-y-1">
                <flux:heading level="2" size="lg">{{ $galleryId ? 'Ubah Foto' : 'Tambah Foto' }}</flux:heading>

                <flux:text size="sm">Teks alt menjelaskan isi foto untuk pengguna pembaca layar dan pencarian gambar.</flux:text>
            </div>

            <flux:input
                wire:model.blur="title"
                label="Judul foto"
                type="text"
                required
                placeholder="Pemasangan PLTS atap 10 kWp di Bintaro"
            />

            <x-admin.image-field
                label="Berkas foto"
                model="imageUpload"
                path-model="image"
                :preview="$this->imagePreview"
                description="Bisa juga menempel path dari pustaka media, misalnya galleries/plts-bintaro.jpg"
            />

            <flux:input
                wire:model.blur="alt"
                label="Teks alt"
                description="Kosongkan untuk memakai judul foto."
                type="text"
                placeholder="Deretan panel surya di atap rumah dua lantai"
            />

            <div class="grid gap-4 sm:grid-cols-2">
                <flux:input
                    wire:model.blur="group"
                    label="Grup"
                    description="Dipakai sebagai penyaring di halaman galeri."
                    type="text"
                    placeholder="Residensial"
                    list="daftar-grup-galeri"
                />

                <flux:input
                    wire:model.blur="sort_order"
                    label="Urutan"
                    description="Angka kecil tampil lebih dulu."
                    type="number"
                    min="0"
                    step="1"
                />
            </div>

            <datalist id="daftar-grup-galeri">
                @foreach ($this->groups as $existing)
                    <option value="{{ $existing }}"></option>
                @endforeach
            </datalist>

            <flux:switch wire:model="is_active" label="Tayangkan foto ini di galeri" />

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

    <x-admin.confirm-delete name="hapus-galeri" title="Hapus foto?">
        “{{ $this->confirmingTitle }}” akan dihapus dari galeri. Berkas di pustaka media tetap tersimpan.
    </x-admin.confirm-delete>
</div>
