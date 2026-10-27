<?php

use App\Models\Tag;
use Flux\Flux;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

new #[Title('Tag')] class extends Component {
    #[Url(as: 'cari')]
    public string $search = '';

    public ?int $tagId = null;

    public string $name = '';

    public string $slug = '';

    public bool $slugTouched = false;

    public ?int $confirmingId = null;

    /**
     * @return array<string, mixed>
     */
    protected function rulesFor(): array
    {
        return [
            'name' => ['required', 'string', 'max:80', Rule::unique('tags', 'name')->ignore($this->tagId)],
            'slug' => ['nullable', 'alpha_dash', 'max:100', Rule::unique('tags', 'slug')->ignore($this->tagId)],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function validationAttributes(): array
    {
        return [
            'name' => 'nama tag',
            'slug' => 'slug',
        ];
    }

    #[Computed]
    public function tags()
    {
        return Tag::query()
            ->withCount('articles')
            ->when(
                filled($this->search),
                fn ($query) => $query->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('slug', 'like', '%'.Str::slug($this->search).'%'),
            )
            ->orderBy('name')
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
        $this->reset(['tagId', 'name', 'slug']);
        $this->slugTouched = false;
        $this->resetValidation();

        $this->dispatch('modal-show', name: 'tag');
    }

    public function edit(int $tagId): void
    {
        $tag = Tag::query()->findOrFail($tagId);

        $this->tagId = $tag->id;
        $this->name = (string) $tag->name;
        $this->slug = (string) $tag->slug;
        $this->slugTouched = true;
        $this->resetValidation();

        $this->dispatch('modal-show', name: 'tag');
    }

    public function save(): void
    {
        $data = $this->validate($this->rulesFor(), attributes: $this->validationAttributes());

        $data['slug'] = filled($data['slug']) ? $data['slug'] : null;

        $tag = $this->tagId ? Tag::query()->findOrFail($this->tagId) : new Tag;

        $tag->fill($data);
        $tag->save();

        Flux::toast(
            variant: 'success',
            text: $tag->wasRecentlyCreated ? 'Tag ditambahkan.' : 'Tag diperbarui.',
        );

        $this->dispatch('modal-close', name: 'tag');
        $this->reset(['tagId', 'name', 'slug']);
    }

    public function confirmDelete(int $tagId): void
    {
        $this->confirmingId = $tagId;

        $this->dispatch('modal-show', name: 'hapus-tag');
    }

    public function destroy(): void
    {
        Tag::query()->findOrFail($this->confirmingId)->delete();

        Flux::toast(variant: 'success', text: 'Tag dihapus dari seluruh artikel.');

        $this->confirmingId = null;
        $this->dispatch('modal-close', name: 'hapus-tag');
    }

    #[Computed]
    public function confirmingName(): string
    {
        return $this->confirmingId
            ? (string) Tag::query()->whereKey($this->confirmingId)->value('name')
            : '';
    }
}; ?>

<div class="space-y-6">
    <x-admin.page-header
        title="Tag"
        description="Tag membantu pembaca menemukan artikel sejenis. Tag juga dibuat otomatis saat menulis artikel."
    >
        <flux:button variant="primary" icon="plus" type="button" wire:click="create">
            Tambah Tag
        </flux:button>
    </x-admin.page-header>

    <flux:card>
        <flux:input
            wire:model.live.debounce.400ms="search"
            label="Cari tag"
            icon="magnifying-glass"
            placeholder="Ketik nama tag…"
            type="search"
            class="max-w-sm"
        />
    </flux:card>

    @if ($this->tags->isEmpty())
        <x-admin.empty-state
            title="Tidak ada tag"
            description="Belum ada tag yang cocok dengan pencarian Anda."
            icon="tag"
        >
            <flux:button variant="primary" icon="plus" type="button" wire:click="create">
                Tambah Tag
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
                        <flux:table.column align="end">Aksi</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @foreach ($this->tags as $tag)
                            <flux:table.row wire:key="tag-{{ $tag->id }}">
                                <flux:table.cell variant="strong">{{ $tag->name }}</flux:table.cell>

                                <flux:table.cell class="hidden md:table-cell">
                                    <code class="text-xs">/blog/tag/{{ $tag->slug }}</code>
                                </flux:table.cell>

                                <flux:table.cell align="center">
                                    <flux:badge color="zinc" inset="left right">{{ $tag->articles_count }}</flux:badge>
                                </flux:table.cell>

                                <flux:table.cell align="end">
                                    <div class="flex justify-end gap-1">
                                        <flux:button
                                            size="xs"
                                            variant="ghost"
                                            icon="pencil-square"
                                            type="button"
                                            wire:click="edit({{ $tag->id }})"
                                        >
                                            <span class="sr-only">Ubah {{ $tag->name }}</span>
                                        </flux:button>

                                        <flux:button
                                            size="xs"
                                            variant="ghost"
                                            icon="trash"
                                            type="button"
                                            class="text-red-500"
                                            wire:click="confirmDelete({{ $tag->id }})"
                                        >
                                            <span class="sr-only">Hapus {{ $tag->name }}</span>
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

    <flux:modal name="tag" class="min-w-[22rem] space-y-5">
        <form wire:submit="save" class="space-y-5">
            <div class="space-y-1">
                <flux:heading level="2" size="lg">{{ $tagId ? 'Ubah Tag' : 'Tambah Tag' }}</flux:heading>

                <flux:text size="sm">Slug dibuat otomatis dari nama bila dikosongkan.</flux:text>
            </div>

            <flux:input wire:model.blur="name" label="Nama tag" type="text" required placeholder="panel surya" />

            <flux:input wire:model.blur="slug" label="Slug" type="text" placeholder="panel-surya" />

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

    <x-admin.confirm-delete name="hapus-tag" title="Hapus tag?">
        Tag “{{ $this->confirmingName }}” akan dilepas dari semua artikel. Artikelnya sendiri tidak dihapus.
    </x-admin.confirm-delete>
</div>
