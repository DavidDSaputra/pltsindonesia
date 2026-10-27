<?php

use App\Models\Media;
use App\Models\Project;
use Flux\Flux;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

new #[Title('Proyek')] class extends Component {
    use WithFileUploads;

    public ?int $projectId = null;

    public string $title = '';

    public string $slug = '';

    public bool $slugTouched = false;

    public string $client = '';

    public string $location = '';

    public string $capacity_kwp = '';

    public string $completed_on = '';

    public string $summary = '';

    public string $body = '';

    public string $image = '';

    /** @var TemporaryUploadedFile|null */
    public $imageUpload = null;

    public bool $is_featured = false;

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
            'slug' => ['nullable', 'alpha_dash', 'max:160', Rule::unique('projects', 'slug')->ignore($this->projectId)],
            'client' => ['nullable', 'string', 'max:120'],
            'location' => ['nullable', 'string', 'max:140'],
            'capacity_kwp' => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'completed_on' => ['nullable', 'date'],
            'summary' => ['nullable', 'string', 'max:280'],
            'body' => ['nullable', 'string', 'max:20000'],
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
            'title' => 'judul proyek',
            'slug' => 'slug',
            'client' => 'klien',
            'location' => 'lokasi',
            'capacity_kwp' => 'kapasitas (kWp)',
            'completed_on' => 'tanggal selesai',
            'summary' => 'ringkasan',
            'body' => 'isi proyek',
            'image' => 'gambar',
            'imageUpload' => 'berkas gambar',
            'sort_order' => 'urutan',
        ];
    }

    #[Computed]
    public function projects()
    {
        return Project::query()->ordered()->get();
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
        $this->completed_on = now()->format('Y-m-d');
        $this->dispatch('modal-show', name: 'proyek');
    }

    public function edit(int $projectId): void
    {
        $project = Project::query()->findOrFail($projectId);

        $this->projectId = $project->id;
        $this->title = (string) $project->title;
        $this->slug = (string) $project->slug;
        $this->slugTouched = true;
        $this->client = (string) $project->client;
        $this->location = (string) $project->location;
        $this->capacity_kwp = $project->capacity_kwp !== null ? (string) $project->capacity_kwp : '';
        $this->completed_on = $project->completed_on?->format('Y-m-d') ?? '';
        $this->summary = (string) $project->summary;
        $this->body = (string) $project->body;
        $this->image = (string) $project->image;
        $this->is_featured = (bool) $project->is_featured;
        $this->is_active = (bool) $project->is_active;
        $this->sort_order = (string) $project->sort_order;

        $this->imageUpload = null;
        $this->resetValidation();

        $this->dispatch('modal-show', name: 'proyek');
    }

    public function save(): void
    {
        $data = $this->validate($this->rulesFor(), attributes: $this->validationAttributes());

        $image = $data['image'] ?? '';

        if ($this->imageUpload instanceof TemporaryUploadedFile) {
            $image = Media::storeUpload($this->imageUpload, Auth::id(), 'projects')->path;
        }

        unset($data['imageUpload']);

        $data['image'] = filled($image) ? $image : null;
        $data['slug'] = filled($data['slug']) ? $data['slug'] : null;
        $data['capacity_kwp'] = filled($data['capacity_kwp']) ? (float) $data['capacity_kwp'] : null;
        $data['completed_on'] = filled($data['completed_on']) ? Carbon::parse($data['completed_on'])->startOfDay() : null;
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['is_featured'] = $this->is_featured;
        $data['is_active'] = $this->is_active;

        $project = $this->projectId ? Project::query()->findOrFail($this->projectId) : new Project;

        $project->fill($data);
        $project->save();

        Flux::toast(
            variant: 'success',
            text: $project->wasRecentlyCreated ? 'Proyek ditambahkan.' : 'Proyek diperbarui.',
        );

        $this->dispatch('modal-close', name: 'proyek');
        $this->resetForm();
    }

    public function toggleActive(int $projectId): void
    {
        $project = Project::query()->findOrFail($projectId);

        $project->update(['is_active' => ! $project->is_active]);

        Flux::toast(
            variant: 'success',
            text: $project->is_active ? 'Proyek ditayangkan.' : 'Proyek disembunyikan.',
        );
    }

    public function confirmDelete(int $projectId): void
    {
        $this->confirmingId = $projectId;

        $this->dispatch('modal-show', name: 'hapus-proyek');
    }

    public function destroy(): void
    {
        Project::query()->findOrFail($this->confirmingId)->delete();

        Flux::toast(variant: 'success', text: 'Proyek dihapus.');

        $this->confirmingId = null;
        $this->dispatch('modal-close', name: 'hapus-proyek');
    }

    #[Computed]
    public function confirmingTitle(): string
    {
        return $this->confirmingId
            ? (string) Project::query()->whereKey($this->confirmingId)->value('title')
            : '';
    }

    protected function resetForm(): void
    {
        $this->reset([
            'projectId', 'title', 'slug', 'client', 'location', 'capacity_kwp',
            'completed_on', 'summary', 'body', 'image', 'sort_order', 'confirmingId',
        ]);

        $this->is_featured = false;
        $this->is_active = true;
        $this->slugTouched = false;
        $this->imageUpload = null;
        $this->resetValidation();
    }
}; ?>

<div class="space-y-6">
    <x-admin.page-header
        title="Proyek"
        description="Portofolio pemasangan PLTS yang tampil di beranda dan halaman /proyek."
    >
        <flux:button variant="primary" icon="plus" type="button" wire:click="create">
            Tambah Proyek
        </flux:button>
    </x-admin.page-header>

    @if ($this->projects->isEmpty())
        <x-admin.empty-state
            title="Belum ada proyek"
            description="Dokumentasikan pemasangan yang sudah selesai untuk membangun kepercayaan calon pelanggan."
            icon="briefcase"
        >
            <flux:button variant="primary" icon="plus" type="button" wire:click="create">
                Tambah Proyek
            </flux:button>
        </x-admin.empty-state>
    @else
        <flux:card class="overflow-hidden">
            <div class="-mx-4 overflow-x-auto sm:mx-0">
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>Proyek</flux:table.column>
                        <flux:table.column class="hidden md:table-cell">Lokasi</flux:table.column>
                        <flux:table.column class="hidden lg:table-cell">Kapasitas</flux:table.column>
                        <flux:table.column class="hidden lg:table-cell">Selesai</flux:table.column>
                        <flux:table.column align="center">Status</flux:table.column>
                        <flux:table.column align="end">Aksi</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @foreach ($this->projects as $project)
                            <flux:table.row wire:key="proyek-{{ $project->id }}">
                                <flux:table.cell>
                                    <div class="flex items-start gap-3">
                                        @if ($project->image_url)
                                            <img src="{{ $project->image_url }}" alt="" class="size-12 shrink-0 rounded-lg object-cover" loading="lazy">
                                        @else
                                            <span class="grid size-12 shrink-0 place-items-center rounded-lg bg-zinc-100 dark:bg-zinc-700/50">
                                                <flux:icon name="photo" class="size-5 text-zinc-400" />
                                            </span>
                                        @endif

                                        <div class="min-w-0">
                                            <p class="max-w-64 truncate font-medium text-zinc-800 dark:text-zinc-100">
                                                {{ $project->title }}
                                            </p>

                                            <flux:text size="sm" class="truncate">
                                                {{ $project->client ?: 'Klien tidak dicantumkan' }}
                                            </flux:text>

                                            @if ($project->is_featured)
                                                <flux:badge size="sm" color="amber" inset="top">Unggulan</flux:badge>
                                            @endif
                                        </div>
                                    </div>
                                </flux:table.cell>

                                <flux:table.cell class="hidden md:table-cell">{{ $project->location ?: '—' }}</flux:table.cell>

                                <flux:table.cell class="hidden lg:table-cell">{{ $project->capacity_label ?? '—' }}</flux:table.cell>

                                <flux:table.cell class="hidden lg:table-cell">
                                    {{ $project->completed_on?->translatedFormat('j M Y') ?? '—' }}
                                </flux:table.cell>

                                <flux:table.cell align="center">
                                    <flux:badge :color="$project->is_active ? 'green' : 'zinc'" inset="left right">
                                        {{ $project->is_active ? 'Tayang' : 'Sembunyi' }}
                                    </flux:badge>
                                </flux:table.cell>

                                <flux:table.cell align="end">
                                    <div class="flex justify-end gap-1">
                                        <flux:button size="xs" variant="ghost" icon="pencil-square" type="button" wire:click="edit({{ $project->id }})">
                                            <span class="sr-only">Ubah {{ $project->title }}</span>
                                        </flux:button>

                                        <flux:button size="xs" variant="ghost" icon="eye" type="button" wire:click="toggleActive({{ $project->id }})">
                                            <span class="sr-only">{{ $project->is_active ? 'Sembunyikan' : 'Tayangkan' }} {{ $project->title }}</span>
                                        </flux:button>

                                        @if ($project->is_active)
                                            <flux:button size="xs" variant="ghost" icon="arrow-top-right-on-square" :href="route('projects.show', $project)" target="_blank">
                                                <span class="sr-only">Lihat {{ $project->title }} di situs</span>
                                            </flux:button>
                                        @endif

                                        <flux:button
                                            size="xs"
                                            variant="ghost"
                                            icon="trash"
                                            type="button"
                                            class="text-red-500"
                                            wire:click="confirmDelete({{ $project->id }})"
                                        >
                                            <span class="sr-only">Hapus {{ $project->title }}</span>
                                        </flux:button>
                                    </div>
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            </div>
        </flux:card>
    @endif

    <flux:modal name="proyek" scroll="body" class="min-w-[24rem] max-w-3xl space-y-5">
        <form wire:submit="save" class="space-y-5">
            <div class="space-y-1">
                <flux:heading level="2" size="lg">{{ $projectId ? 'Ubah Proyek' : 'Tambah Proyek' }}</flux:heading>

                <flux:text size="sm">Data kapasitas dan lokasi dipakai sebagai structured data agar mudah ditemukan.</flux:text>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <flux:input wire:model.blur="title" label="Judul proyek" type="text" required placeholder="PLTS Atap 120 kWp Pabrik Tekstil" />

                <flux:input wire:model.blur="slug" label="Slug" type="text" placeholder="plts-atap-pabrik-tekstil" />
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <flux:input wire:model.blur="client" label="Klien" type="text" placeholder="PT Sumber Rejeki" />

                <flux:input wire:model.blur="location" label="Lokasi" type="text" placeholder="Bekasi, Jawa Barat" />
            </div>

            <div class="grid gap-4 sm:grid-cols-3">
                <flux:input
                    wire:model.blur="capacity_kwp"
                    label="Kapasitas (kWp)"
                    type="number"
                    min="0"
                    step="0.01"
                    placeholder="120"
                />

                <flux:input wire:model.blur="completed_on" label="Tanggal selesai" type="date" />

                <flux:input
                    wire:model.blur="sort_order"
                    label="Urutan"
                    description="Angka kecil tampil lebih dulu."
                    type="number"
                    min="0"
                    step="1"
                />
            </div>

            <flux:textarea
                wire:model.blur="summary"
                label="Ringkasan"
                description="Ditampilkan pada kartu proyek dan hasil pencarian."
                rows="2"
                placeholder="Pemasangan PLTS atap 120 kWp dengan skema ekspor-impor ke PLN."
            />

            <flux:textarea
                wire:model.blur="body"
                label="Isi halaman (Markdown)"
                rows="10"
                placeholder="## Tantangan&#10;&#10;Jelaskan kondisi awal, solusi yang dipasang, dan hasilnya."
            />

            <x-admin.image-field
                label="Foto utama proyek"
                model="imageUpload"
                path-model="image"
                :preview="$this->imagePreview"
                description="Rasio 4:3 atau 16:9 tampil paling baik."
            />

            <div class="grid gap-3 sm:grid-cols-2">
                <flux:switch wire:model="is_active" label="Tayangkan proyek ini di situs" />

                <flux:switch wire:model="is_featured" label="Tandai sebagai proyek unggulan" />
            </div>

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

    <x-admin.confirm-delete name="hapus-proyek" title="Hapus proyek?">
        Proyek “{{ $this->confirmingTitle }}” akan hilang dari portofolio. Tautan lamanya akan menghasilkan halaman 404.
    </x-admin.confirm-delete>
</div>
