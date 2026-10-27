<?php

use App\Models\Category;
use Flux\Flux;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Kategori')] class extends Component {
    public ?int $categoryId = null;

    public string $name = '';

    public string $slug = '';

    public bool $slugTouched = false;

    public string $description = '';

    public string $sort_order = '0';

    /**
     * Id of the category queued for deletion, driving the confirm modal.
     */
    public ?int $confirmingId = null;

    /**
     * @return array<string, mixed>
     */
    protected function rulesFor(): array
    {
        return [
            'name' => ['required', 'string', 'max:120', Rule::unique('categories', 'name')->ignore($this->categoryId)],
            'slug' => ['nullable', 'alpha_dash', 'max:140', Rule::unique('categories', 'slug')->ignore($this->categoryId)],
            'description' => ['nullable', 'string', 'max:320'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function validationAttributes(): array
    {
        return [
            'name' => 'nama kategori',
            'slug' => 'slug',
            'description' => 'deskripsi',
            'sort_order' => 'urutan',
        ];
    }

    #[Computed]
    public function categories()
    {
        return Category::query()
            ->withCount('articles')
            ->ordered()
            ->get();
    }

    public function updatedName(): void
    {
        if (! $this->slugTouched) {
            $this->slug = Str::slug($this->name);
        }
    }

    public function updatedSlug(): void
    {
        $this->slugTouched = $this->slug !== '';
        $this->slug = Str::slug($this->slug);
    }

    public function create(): void
    {
        $this->reset(['categoryId', 'name', 'slug', 'description', 'sort_order']);
        $this->slugTouched = false;
        $this->resetValidation();

        $this->sort_order = (string) ((int) Category::query()->max('sort_order') + 10);

        $this->dispatch('modal-show', name: 'kategori');
    }

    public function edit(int $categoryId): void
    {
        $category = Category::query()->findOrFail($categoryId);

        $this->categoryId = $category->id;
        $this->name = (string) $category->name;
        $this->slug = (string) $category->slug;
        $this->slugTouched = true;
        $this->description = (string) $category->description;
        $this->sort_order = (string) $category->sort_order;
        $this->resetValidation();

        $this->dispatch('modal-show', name: 'kategori');
    }

    public function save(): void
    {
        $data = $this->validate($this->rulesFor(), attributes: $this->validationAttributes());

        $data['slug'] = filled($data['slug']) ? $data['slug'] : null;
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        $category = $this->categoryId
            ? Category::query()->findOrFail($this->categoryId)
            : new Category;

        $category->fill($data);
        $category->save();

        Flux::toast(
            variant: 'success',
            text: $category->wasRecentlyCreated ? 'Kategori ditambahkan.' : 'Kategori diperbarui.',
        );

        $this->dispatch('modal-close', name: 'kategori');
        $this->reset(['categoryId', 'name', 'slug', 'description', 'sort_order']);
    }

    public function confirmDelete(int $categoryId): void
    {
        $this->confirmingId = $categoryId;

        $this->dispatch('modal-show', name: 'hapus-kategori');
    }

    public function destroy(): void
    {
        $category = Category::query()->findOrFail($this->confirmingId);

        $category->delete();

        Flux::toast(variant: 'success', text: 'Kategori dihapus. Artikel terkait kini tanpa kategori.');

        $this->confirmingId = null;
        $this->dispatch('modal-close', name: 'hapus-kategori');
    }

    #[Computed]
    public function confirmingName(): string
    {
        return $this->confirmingId
            ? (string) Category::query()->whereKey($this->confirmingId)->value('name')
            : '';
    }
}; ?>

<div class="space-y-6">
    <x-admin.page-header
        title="Kategori"
        description="Kelompokkan artikel agar navigasi blog dan breadcrumb lebih jelas bagi pembaca maupun mesin pencari."
    >
        <flux:button variant="primary" icon="plus" type="button" wire:click="create">
            Tambah Kategori
        </flux:button>
    </x-admin.page-header>

    @if ($this->categories->isEmpty())
        <x-admin.empty-state
            title="Belum ada kategori"
            description="Buat kategori seperti “Panduan PLTS”, “Produk”, atau “Berita Proyek” untuk mulai mengelompokkan artikel."
            icon="folder"
        >
            <flux:button variant="primary" icon="plus" type="button" wire:click="create">
                Tambah Kategori
            </flux:button>
        </x-admin.empty-state>
    @else
        <flux:card class="overflow-hidden">
            <div class="-mx-4 overflow-x-auto sm:mx-0">
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>Nama</flux:table.column>
                        <flux:table.column class="hidden md:table-cell">Slug</flux:table.column>
                        <flux:table.column align="center">Artikel</flux:table.column>
                        <flux:table.column align="center">Urutan</flux:table.column>
                        <flux:table.column align="end">Aksi</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @foreach ($this->categories as $category)
                            <flux:table.row wire:key="kategori-{{ $category->id }}">
                                <flux:table.cell variant="strong">
                                    <div class="max-w-80">
                                        <p class="truncate">{{ $category->name }}</p>

                                        @if (filled($category->description))
                                            <flux:text size="sm" class="line-clamp-2">{{ $category->description }}</flux:text>
                                        @endif
                                    </div>
                                </flux:table.cell>

                                <flux:table.cell class="hidden md:table-cell">
                                    <code class="text-xs">/blog/kategori/{{ $category->slug }}</code>
                                </flux:table.cell>

                                <flux:table.cell align="center">
                                    <flux:badge color="zinc" inset="left right">{{ $category->articles_count }}</flux:badge>
                                </flux:table.cell>

                                <flux:table.cell align="center">{{ $category->sort_order }}</flux:table.cell>

                                <flux:table.cell align="end">
                                    <div class="flex justify-end gap-1">
                                        <flux:button
                                            size="xs"
                                            variant="ghost"
                                            icon="pencil-square"
                                            type="button"
                                            wire:click="edit({{ $category->id }})"
                                        >
                                            <span class="sr-only">Ubah {{ $category->name }}</span>
                                        </flux:button>

                                        <flux:button
                                            size="xs"
                                            variant="ghost"
                                            icon="trash"
                                            type="button"
                                            class="text-red-500"
                                            wire:click="confirmDelete({{ $category->id }})"
                                        >
                                            <span class="sr-only">Hapus {{ $category->name }}</span>
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

    <flux:modal name="kategori" class="min-w-[24rem] space-y-5">
        <form wire:submit="save" class="space-y-5">
            <div class="space-y-1">
                <flux:heading level="2" size="lg">{{ $categoryId ? 'Ubah Kategori' : 'Tambah Kategori' }}</flux:heading>

                <flux:text size="sm">Slug dibuat otomatis dari nama bila dikosongkan.</flux:text>
            </div>

            <flux:input wire:model.blur="name" label="Nama kategori" type="text" required placeholder="Panduan PLTS" />

            <flux:input wire:model.blur="slug" label="Slug" type="text" placeholder="panduan-plts" />

            <flux:textarea
                wire:model.blur="description"
                label="Deskripsi"
                description="Opsional. Dipakai sebagai pengantar halaman kategori."
                rows="3"
                placeholder="Kumpulan panduan praktis seputar pemasangan PLTS."
            />

            <flux:input
                wire:model.blur="sort_order"
                label="Urutan"
                description="Angka kecil tampil lebih dulu."
                type="number"
                min="0"
                step="1"
            />

            <div class="flex gap-2">
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

    <x-admin.confirm-delete name="hapus-kategori" title="Hapus kategori?">
        Kategori “{{ $this->confirmingName }}” akan dihapus. Artikel di dalamnya tetap ada
        tetapi berubah menjadi tanpa kategori.
    </x-admin.confirm-delete>
</div>
