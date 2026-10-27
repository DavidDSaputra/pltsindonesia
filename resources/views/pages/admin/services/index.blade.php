<?php

use App\Models\Media;
use App\Models\Service;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

new #[Title('Layanan')] class extends Component {
    use WithFileUploads;

    /**
     * Icons available to <x-site.icon> that make sense for a service card.
     *
     * @var array<int, string>
     */
    public const array ICONS = [
        'sun', 'bolt', 'panels', 'battery', 'wrench', 'shield', 'chart', 'leaf',
        'home', 'building', 'users', 'gear', 'wallet', 'layers', 'globe', 'server',
        'spark', 'book', 'save', 'filter',
    ];

    public ?int $serviceId = null;

    public string $title = '';

    public string $slug = '';

    public bool $slugTouched = false;

    public string $icon = 'sun';

    public string $summary = '';

    public string $body = '';

    public string $features = '';

    public string $image = '';

    /** @var TemporaryUploadedFile|null */
    public $imageUpload = null;

    public bool $is_active = true;

    public string $sort_order = '0';

    public ?int $confirmingId = null;

    /**
     * @return array<string, mixed>
     */
    protected function rulesFor(): array
    {
        return [
            'title' => ['required', 'string', 'max:140'],
            'slug' => ['nullable', 'alpha_dash', 'max:160', Rule::unique('services', 'slug')->ignore($this->serviceId)],
            'icon' => ['required', 'string', Rule::in(self::ICONS)],
            'summary' => ['nullable', 'string', 'max:240'],
            'body' => ['nullable', 'string', 'max:20000'],
            'features' => ['nullable', 'string', 'max:2000'],
            'image' => ['nullable', 'string', 'max:500'],
            'imageUpload' => ['nullable', 'image', 'max:4096'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function validationAttributes(): array
    {
        return [
            'title' => 'judul layanan',
            'slug' => 'slug',
            'icon' => 'ikon',
            'summary' => 'ringkasan',
            'body' => 'isi layanan',
            'features' => 'daftar keunggulan',
            'image' => 'gambar',
            'imageUpload' => 'berkas gambar',
            'sort_order' => 'urutan',
        ];
    }

    #[Computed]
    public function services()
    {
        return Service::query()->ordered()->get();
    }

    /**
     * Icon choices for the select box.
     *
     * @return array<int, string>
     */
    #[Computed]
    public function iconOptions(): array
    {
        return self::ICONS;
    }

    public function updatedTitle(): void
    {
        if (! $this->slugTouched) {
            $this->slug = Str::slug($this->title);
        }
    }

    public function updatedSlug(): void
    {
        $this->slugTouched = $this->slug !== '';
        $this->slug = Str::slug($this->slug);
    }

    public function updatedImageUpload(): void
    {
        $this->validate(['imageUpload' => ['nullable', 'image', 'max:4096']]);
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
        $this->sort_order = (string) ((int) Service::query()->max('sort_order') + 10);
        $this->dispatch('modal-show', name: 'layanan');
    }

    public function edit(int $serviceId): void
    {
        $service = Service::query()->findOrFail($serviceId);

        $this->serviceId = $service->id;
        $this->title = (string) $service->title;
        $this->slug = (string) $service->slug;
        $this->slugTouched = true;
        $this->icon = (string) $service->icon;
        $this->summary = (string) $service->summary;
        $this->body = (string) $service->body;
        $this->features = implode("\n", (array) $service->features);
        $this->image = (string) $service->image;
        $this->is_active = (bool) $service->is_active;
        $this->sort_order = (string) $service->sort_order;

        $this->imageUpload = null;
        $this->resetValidation();

        $this->dispatch('modal-show', name: 'layanan');
    }

    public function save(): void
    {
        $data = $this->validate($this->rulesFor(), attributes: $this->validationAttributes());

        $image = $data['image'] ?? '';

        if ($this->imageUpload instanceof TemporaryUploadedFile) {
            $image = Media::storeUpload($this->imageUpload, Auth::id(), 'services')->path;
        }

        unset($data['imageUpload'], $data['features']);

        $data['image'] = filled($image) ? $image : null;
        $data['slug'] = filled($data['slug']) ? $data['slug'] : null;
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['is_active'] = $this->is_active;
        $data['features'] = $this->parseFeatures();

        $service = $this->serviceId ? Service::query()->findOrFail($this->serviceId) : new Service;

        $service->fill($data);
        $service->save();

        Flux::toast(
            variant: 'success',
            text: $service->wasRecentlyCreated ? 'Layanan ditambahkan.' : 'Layanan diperbarui.',
        );

        $this->dispatch('modal-close', name: 'layanan');
        $this->resetForm();
    }

    /**
     * Turn the "one per line" textarea into a clean list.
     *
     * @return array<int, string>
     */
    protected function parseFeatures(): array
    {
        return collect(explode("\n", $this->features))
            ->map(fn (string $line) => trim($line, " \t-*•\r\n"))
            ->filter()
            ->values()
            ->all();
    }

    public function toggleActive(int $serviceId): void
    {
        $service = Service::query()->findOrFail($serviceId);

        $service->update(['is_active' => ! $service->is_active]);

        Flux::toast(
            variant: 'success',
            text: $service->is_active ? 'Layanan ditayangkan.' : 'Layanan disembunyikan.',
        );
    }

    public function confirmDelete(int $serviceId): void
    {
        $this->confirmingId = $serviceId;

        $this->dispatch('modal-show', name: 'hapus-layanan');
    }

    public function destroy(): void
    {
        Service::query()->findOrFail($this->confirmingId)->delete();

        Flux::toast(variant: 'success', text: 'Layanan dihapus.');

        $this->confirmingId = null;
        $this->dispatch('modal-close', name: 'hapus-layanan');
    }

    #[Computed]
    public function confirmingTitle(): string
    {
        return $this->confirmingId
            ? (string) Service::query()->whereKey($this->confirmingId)->value('title')
            : '';
    }

    protected function resetForm(): void
    {
        $this->reset([
            'serviceId', 'title', 'slug', 'summary', 'body', 'features', 'image', 'sort_order', 'confirmingId',
        ]);

        $this->icon = 'sun';
        $this->is_active = true;
        $this->slugTouched = false;
        $this->imageUpload = null;
        $this->resetValidation();
    }
}; ?>

<div class="space-y-6">
    <x-admin.page-header
        title="Layanan"
        description="Layanan yang tampil di beranda dan halaman /layanan. Urutan menentukan posisi kartu."
    >
        <flux:button variant="primary" icon="plus" type="button" wire:click="create">
            Tambah Layanan
        </flux:button>
    </x-admin.page-header>

    @if ($this->services->isEmpty())
        <x-admin.empty-state
            title="Belum ada layanan"
            description="Tambahkan layanan seperti “PLTS Atap Rumah”, “PLTS Industri”, atau “Perawatan Panel Surya”."
            icon="wrench-screwdriver"
        >
            <flux:button variant="primary" icon="plus" type="button" wire:click="create">
                Tambah Layanan
            </flux:button>
        </x-admin.empty-state>
    @else
        <ul class="grid gap-4 lg:grid-cols-2" role="list">
            @foreach ($this->services as $service)
                <li
                    wire:key="layanan-{{ $service->id }}"
                    class="flex gap-4 rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-800"
                >
                    <div class="shrink-0">
                        @if ($service->image_url)
                            <img src="{{ $service->image_url }}" alt="" class="size-16 rounded-lg object-cover" loading="lazy">
                        @else
                            <span class="grid size-16 place-items-center rounded-lg bg-zinc-100 text-zinc-500 dark:bg-zinc-700/50 dark:text-zinc-300">
                                <x-site.icon :name="$service->icon" class="size-7" />
                            </span>
                        @endif
                    </div>

                    <div class="min-w-0 flex-1 space-y-1.5">
                        <div class="flex flex-wrap items-center gap-2">
                            <flux:heading level="2" size="lg" class="truncate">{{ $service->title }}</flux:heading>

                            <flux:badge :color="$service->is_active ? 'green' : 'zinc'" size="sm" inset="left right">
                                {{ $service->is_active ? 'Tayang' : 'Sembunyi' }}
                            </flux:badge>

                            <flux:badge color="zinc" size="sm" inset="left right">Urutan {{ $service->sort_order }}</flux:badge>
                        </div>

                        <flux:text size="sm" class="line-clamp-2">
                            {{ $service->summary ?: 'Belum ada ringkasan.' }}
                        </flux:text>

                        <flux:text size="sm">
                            {{ count((array) $service->features) }} keunggulan ·
                            <code class="text-xs">/layanan/{{ $service->slug }}</code>
                        </flux:text>

                        <div class="flex flex-wrap gap-1 pt-1">
                            <flux:button size="xs" variant="ghost" icon="pencil-square" type="button" wire:click="edit({{ $service->id }})">
                                Ubah
                            </flux:button>

                            <flux:button size="xs" variant="ghost" icon="eye" type="button" wire:click="toggleActive({{ $service->id }})">
                                {{ $service->is_active ? 'Sembunyikan' : 'Tayangkan' }}
                            </flux:button>

                            @if ($service->is_active)
                                <flux:button size="xs" variant="ghost" icon="arrow-top-right-on-square" :href="route('services.show', $service)" target="_blank">
                                    Lihat
                                </flux:button>
                            @endif

                            <flux:button
                                size="xs"
                                variant="ghost"
                                icon="trash"
                                type="button"
                                class="text-red-500"
                                wire:click="confirmDelete({{ $service->id }})"
                            >
                                Hapus
                            </flux:button>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif

    <flux:modal name="layanan" scroll="body" class="min-w-[24rem] max-w-3xl space-y-5">
        <form wire:submit="save" class="space-y-5">
            <div class="space-y-1">
                <flux:heading level="2" size="lg">{{ $serviceId ? 'Ubah Layanan' : 'Tambah Layanan' }}</flux:heading>

                <flux:text size="sm">Isi ditulis dalam Markdown dan tampil pada halaman detail layanan.</flux:text>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <flux:input wire:model.blur="title" label="Judul layanan" type="text" required placeholder="PLTS Atap Rumah" />

                <flux:input wire:model.blur="slug" label="Slug" type="text" placeholder="plts-atap-rumah" />
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <flux:select wire:model.blur="icon" label="Ikon kartu">
                    @foreach ($this->iconOptions as $option)
                        <flux:select.option value="{{ $option }}">{{ $option }}</flux:select.option>
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
            </div>

            <div class="flex items-center gap-3 rounded-xl border border-zinc-200 p-3 dark:border-zinc-700">
                <span class="grid size-11 shrink-0 place-items-center rounded-lg bg-zinc-100 text-zinc-700 dark:bg-zinc-700/50 dark:text-zinc-200">
                    <x-site.icon :name="$icon" class="size-6" />
                </span>

                <flux:text size="sm">
                    Pratinjau ikon kartu layanan. Ikon digambar sendiri agar ringan dan tajam di semua layar.
                </flux:text>
            </div>

            <flux:textarea
                wire:model.blur="summary"
                label="Ringkasan"
                description="Satu kalimat untuk kartu layanan dan meta description."
                rows="2"
                placeholder="Pasang PLTS atap untuk menekan tagihan listrik rumah hingga 40%."
            />

            <flux:textarea
                wire:model.blur="features"
                label="Keunggulan"
                description="Satu baris untuk setiap poin. Ditampilkan sebagai daftar centang."
                rows="4"
                placeholder="Survei lokasi gratis&#10;Garansi panel 25 tahun&#10;Bantuan pengurusan izin PLN"
            />

            <flux:textarea
                wire:model.blur="body"
                label="Isi halaman (Markdown)"
                rows="10"
                placeholder="## Cara kerja&#10;&#10;Jelaskan proses pemasangan dari survei hingga commissioning."
            />

            <x-admin.image-field
                label="Gambar layanan"
                model="imageUpload"
                path-model="image"
                :preview="$this->imagePreview"
                description="Opsional. Dipakai pada halaman detail layanan."
            />

            <flux:switch wire:model="is_active" label="Tayangkan layanan ini di situs" />

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

    <x-admin.confirm-delete name="hapus-layanan" title="Hapus layanan?">
        Layanan “{{ $this->confirmingTitle }}” akan hilang dari situs. Tautan lamanya akan menghasilkan halaman 404.
    </x-admin.confirm-delete>
</div>
