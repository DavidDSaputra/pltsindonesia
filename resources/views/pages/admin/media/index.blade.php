<?php

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

new #[Title('Pustaka Media')] class extends Component {
    use WithFileUploads, WithPagination;

    #[Url(as: 'cari')]
    public string $search = '';

    /** @var array<int, TemporaryUploadedFile>|null */
    public $uploads = null;

    public ?int $confirmingId = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Persist every selected file as soon as the upload finishes.
     */
    public function updatedUploads(): void
    {
        $this->validate([
            'uploads.*' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,gif,svg,avif', 'max:4096'],
        ], [
            'uploads.*.mimes' => 'Format berkas harus gambar (JPG, PNG, WebP, GIF, SVG, atau AVIF).',
            'uploads.*.max' => 'Ukuran setiap berkas maksimal 4 MB.',
        ], [
            'uploads.*' => 'berkas',
        ]);

        $stored = 0;

        foreach ((array) $this->uploads as $upload) {
            if ($upload instanceof TemporaryUploadedFile) {
                Media::storeUpload($upload, Auth::id());
                $stored++;
            }
        }

        $this->uploads = null;

        Flux::toast(
            variant: 'success',
            text: $stored === 1 ? '1 berkas berhasil diunggah.' : $stored.' berkas berhasil diunggah.',
        );
    }

    #[Computed]
    public function media()
    {
        return Media::query()
            ->with('uploader')
            ->when(
                filled($this->search),
                fn ($query) => $query->where(function ($inner) {
                    $like = '%'.$this->search.'%';

                    $inner->where('title', 'like', $like)
                        ->orWhere('original_name', 'like', $like)
                        ->orWhere('alt', 'like', $like)
                        ->orWhere('path', 'like', $like);
                }),
            )
            ->ordered()
            ->paginate(24)
            ->withQueryString();
    }

    /**
     * Save the alt text for an image; it is used on the public site for accessibility.
     */
    public function updateAlt(int $mediaId, string $alt): void
    {
        Media::query()->findOrFail($mediaId)->update(['alt' => trim($alt)]);
    }

    public function confirmDelete(int $mediaId): void
    {
        $this->confirmingId = $mediaId;

        $this->dispatch('modal-show', name: 'hapus-media');
    }

    public function destroy(): void
    {
        $media = Media::query()->findOrFail($this->confirmingId);

        $media->deleteFile();
        $media->delete();

        Flux::toast(variant: 'success', text: 'Berkas dihapus dari pustaka media.');

        $this->confirmingId = null;
        $this->dispatch('modal-close', name: 'hapus-media');
    }

    #[Computed]
    public function confirmingTitle(): string
    {
        return $this->confirmingId
            ? (string) (Media::query()->whereKey($this->confirmingId)->value('title') ?? '')
            : '';
    }
}; ?>

<div class="space-y-6">
    <x-admin.page-header
        title="Pustaka Media"
        description="Unggah gambar sekali, pakai di banyak tempat: sampul artikel, galeri, layanan, hingga proyek."
    />

    <flux:card class="space-y-4">
        <div class="grid gap-4 sm:grid-cols-[1fr_auto] sm:items-end">
            <flux:input
                wire:model.live.debounce.400ms="search"
                label="Cari berkas"
                icon="magnifying-glass"
                placeholder="Judul, nama berkas, atau path…"
                type="search"
            />

            <div>
                <input
                    id="unggah-media"
                    type="file"
                    multiple
                    accept="image/*"
                    wire:model="uploads"
                    class="block w-full cursor-pointer text-sm text-zinc-500 file:me-3 file:cursor-pointer file:rounded-lg file:border-0 file:bg-zinc-900 file:px-4 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-zinc-700 sm:w-auto dark:text-zinc-400 dark:file:bg-zinc-50 dark:file:text-zinc-900 dark:hover:file:bg-zinc-200"
                >

                <div wire:loading wire:target="uploads" class="mt-2">
                    <flux:text size="sm" color="amber">Mengunggah berkas…</flux:text>
                </div>
            </div>
        </div>

        @foreach ($errors->getMessages() as $key => $keyMessages)
            @if (str_starts_with((string) $key, 'uploads'))
                <flux:error :message="$keyMessages[0] ?? null" />
            @endif
        @endforeach

        <flux:text size="sm">
            Format yang diterima: JPG, PNG, WebP, GIF, SVG, AVIF. Maksimal 4 MB per berkas.
            Isi kolom <strong>alt</strong> agar gambar tetap dapat diakses pembaca layar.
        </flux:text>
    </flux:card>

    @if ($this->media->isEmpty())
        <x-admin.empty-state
            title="Pustaka media masih kosong"
            description="Unggah foto dokumentasi proyek, logo, atau gambar sampul artikel."
            icon="photo"
        />
    @else
        <div class="space-y-4">
            <ul class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6" role="list">
                @foreach ($this->media as $item)
                    <li
                        wire:key="media-{{ $item->id }}"
                        x-data="{ copied: false }"
                        class="group flex flex-col overflow-hidden rounded-xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-800"
                    >
                        <div class="relative aspect-[4/3] bg-zinc-100 dark:bg-zinc-900">
                            <img
                                src="{{ $item->url }}"
                                alt="{{ $item->alt ?: $item->title }}"
                                class="size-full object-cover"
                                loading="lazy"
                            >

                            <div class="absolute inset-x-0 top-0 flex justify-end gap-1 p-2 opacity-0 transition group-focus-within:opacity-100 group-hover:opacity-100">
                                <button
                                    type="button"
                                    x-on:click="navigator.clipboard.writeText(@js($item->url)); copied = true; setTimeout(() => copied = false, 2000)"
                                    class="grid size-7 place-items-center rounded-md bg-white/90 text-zinc-700 shadow-sm hover:bg-white dark:bg-zinc-800/90 dark:text-zinc-200"
                                >
                                    <span class="sr-only">Salin URL {{ $item->title }}</span>
                                    <flux:icon :name="copied ? 'check' : 'link'" variant="mini" class="size-4" />
                                </button>

                                <button
                                    type="button"
                                    wire:click="confirmDelete({{ $item->id }})"
                                    class="grid size-7 place-items-center rounded-md bg-white/90 text-red-500 shadow-sm hover:bg-white dark:bg-zinc-800/90"
                                >
                                    <span class="sr-only">Hapus {{ $item->title }}</span>
                                    <flux:icon name="trash" variant="mini" class="size-4" />
                                </button>
                            </div>

                            <span
                                x-show="copied"
                                x-cloak
                                class="absolute inset-x-2 bottom-2 rounded-md bg-zinc-900/90 px-2 py-1 text-center text-xs font-medium text-white"
                            >
                                URL disalin
                            </span>
                        </div>

                        <div class="flex-1 space-y-2 p-3">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-medium text-zinc-800 dark:text-zinc-100">
                                    {{ $item->title ?: $item->original_name }}
                                </p>

                                <flux:text size="sm">{{ $item->human_size }} · {{ $item->created_at->diffForHumans() }}</flux:text>
                            </div>

                            <flux:input
                                size="sm"
                                type="text"
                                placeholder="Teks alt untuk aksesibilitas"
                                :value="$item->alt"
                                wire:change="updateAlt({{ $item->id }}, $event.target.value)"
                            />
                        </div>
                    </li>
                @endforeach
            </ul>

            <flux:pagination :paginator="$this->media" />
        </div>
    @endif

    <x-admin.confirm-delete name="hapus-media" title="Hapus berkas?">
        “{{ $this->confirmingTitle }}” akan dihapus dari penyimpanan. Artikel atau halaman yang
        masih memakainya akan menampilkan gambar kosong.
    </x-admin.confirm-delete>
</div>
