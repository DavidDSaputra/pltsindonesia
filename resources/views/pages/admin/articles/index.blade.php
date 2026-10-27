<?php

use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Models\Category;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Artikel')] class extends Component {
    use WithPagination;

    #[Url(as: 'cari')]
    public string $search = '';

    #[Url(as: 'status')]
    public string $status = '';

    #[Url(as: 'kategori')]
    public string $categoryId = '';

    public function updating($property): void
    {
        if (in_array($property, ['search', 'status', 'categoryId'], true)) {
            $this->resetPage();
        }
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'status', 'categoryId']);
        $this->resetPage();
    }

    #[Computed]
    public function articles()
    {
        return Article::query()
            ->with(['category', 'author'])
            ->search($this->search)
            ->when($this->status !== '', fn ($query) => $query->where('status', $this->status))
            ->when($this->categoryId !== '', fn ($query) => $query->where('category_id', $this->categoryId))
            ->orderByDesc('updated_at')
            ->paginate(12)
            ->withQueryString();
    }

    #[Computed]
    public function categories()
    {
        return Category::query()->ordered()->get();
    }

    /**
     * Flip an article between draft and published.
     */
    public function toggleStatus(int $articleId): void
    {
        $article = Article::query()->findOrFail($articleId);

        $next = $article->status === ArticleStatus::Published
            ? ArticleStatus::Draft
            : ArticleStatus::Published;

        $article->update(['status' => $next]);

        Flux::toast(
            variant: 'success',
            text: $next === ArticleStatus::Published
                ? 'Artikel diterbitkan.'
                : 'Artikel dikembalikan ke draf.',
        );
    }

    public function toggleFeatured(int $articleId): void
    {
        $article = Article::query()->findOrFail($articleId);

        $article->update(['is_featured' => ! $article->is_featured]);

        Flux::toast(
            variant: 'success',
            text: $article->is_featured ? 'Ditandai sebagai unggulan.' : 'Bukan lagi artikel unggulan.',
        );
    }

    public function destroy(int $articleId): void
    {
        Article::query()->findOrFail($articleId)->delete();

        Flux::toast(variant: 'success', text: 'Artikel dipindahkan ke arsip.');
    }
}; ?>

<div class="space-y-6">
    <x-admin.page-header
        title="Artikel"
        description="Kelola seluruh tulisan blog: draf, artikel terbit, sampul, dan metadata SEO."
        :href="route('admin.articles.create')"
        label="Tulis Artikel"
    />

    <flux:card class="space-y-4">
        <div class="grid gap-4 md:grid-cols-4">
            <flux:input
                wire:model.live.debounce.400ms="search"
                label="Cari"
                icon="magnifying-glass"
                placeholder="Judul, ringkasan, atau isi…"
                type="search"
                class="md:col-span-2"
            />

            <flux:select wire:model.live="status" label="Status" placeholder="Semua status">
                @foreach (ArticleStatus::cases() as $option)
                    <flux:select.option value="{{ $option->value }}">{{ $option->label() }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:select wire:model.live="categoryId" label="Kategori" placeholder="Semua kategori">
                @foreach ($this->categories as $category)
                    <flux:select.option value="{{ $category->id }}">{{ $category->name }}</flux:select.option>
                @endforeach
            </flux:select>
        </div>

        @if ($search !== '' || $status !== '' || $categoryId !== '')
            <flux:button size="sm" variant="ghost" icon="x-mark" wire:click="resetFilters" type="button">
                Bersihkan filter
            </flux:button>
        @endif
    </flux:card>

    @if ($this->articles->isEmpty())
        <x-admin.empty-state
            title="Tidak ada artikel yang cocok"
            description="Ubah kata kunci atau filter, atau tulis artikel baru untuk mulai mengisi blog."
            icon="newspaper"
        >
            <flux:button :href="route('admin.articles.create')" variant="primary" icon="plus" wire:navigate>
                Tulis Artikel
            </flux:button>
        </x-admin.empty-state>
    @else
        <flux:card class="overflow-hidden">
            <div class="-mx-4 overflow-x-auto sm:mx-0">
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>Judul</flux:table.column>
                        <flux:table.column class="hidden md:table-cell">Kategori</flux:table.column>
                        <flux:table.column>Status</flux:table.column>
                        <flux:table.column class="hidden lg:table-cell">Tayang</flux:table.column>
                        <flux:table.column class="hidden sm:table-cell" align="end">Dilihat</flux:table.column>
                        <flux:table.column align="end">Aksi</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @foreach ($this->articles as $article)
                            <flux:table.row wire:key="artikel-{{ $article->id }}">
                                <flux:table.cell>
                                    <div class="flex items-start gap-3">
                                        @if ($article->cover_url)
                                            <img
                                                src="{{ $article->cover_url }}"
                                                alt=""
                                                class="size-10 shrink-0 rounded-lg object-cover"
                                                loading="lazy"
                                            >
                                        @else
                                            <span class="grid size-10 shrink-0 place-items-center rounded-lg bg-zinc-100 dark:bg-zinc-700/50">
                                                <flux:icon name="photo" class="size-4 text-zinc-400" />
                                            </span>
                                        @endif

                                        <div class="min-w-0">
                                            <flux:link
                                                class="block max-w-72 truncate font-medium"
                                                :href="route('admin.articles.edit', $article)"
                                                wire:navigate
                                            >
                                                {{ $article->title }}
                                            </flux:link>

                                            <flux:text size="sm" class="flex flex-wrap items-center gap-x-2">
                                                <span class="truncate">/blog/{{ $article->slug }}</span>

                                                @if ($article->is_featured)
                                                    <flux:badge size="sm" color="amber" inset="left right">Unggulan</flux:badge>
                                                @endif
                                            </flux:text>
                                        </div>
                                    </div>
                                </flux:table.cell>

                                <flux:table.cell class="hidden md:table-cell">
                                    {{ $article->category?->name ?? '—' }}
                                </flux:table.cell>

                                <flux:table.cell>
                                    <flux:badge :color="$article->status->color()" inset="left right">
                                        {{ $article->status->label() }}
                                    </flux:badge>
                                </flux:table.cell>

                                <flux:table.cell class="hidden lg:table-cell">
                                    {{ $article->published_at?->translatedFormat('j M Y, H.i') ?? '—' }}
                                </flux:table.cell>

                                <flux:table.cell class="hidden sm:table-cell" align="end">
                                    {{ number_format($article->views_count, 0, ',', '.') }}
                                </flux:table.cell>

                                <flux:table.cell align="end">
                                    <flux:dropdown position="bottom" align="end">
                                        <flux:button size="sm" variant="ghost" icon="ellipsis-horizontal" square>
                                            <span class="sr-only">Aksi untuk {{ $article->title }}</span>
                                        </flux:button>

                                        <flux:menu>
                                            <flux:menu.item
                                                :href="route('admin.articles.edit', $article)"
                                                icon="pencil-square"
                                                wire:navigate
                                            >
                                                Ubah
                                            </flux:menu.item>

                                            @if ($article->isPublished())
                                                <flux:menu.item :href="route('blog.show', $article)" icon="eye" target="_blank">
                                                    Lihat di situs
                                                </flux:menu.item>
                                            @endif

                                            <flux:menu.item wire:click="toggleStatus({{ $article->id }})" icon="arrow-path">
                                                {{ $article->status === ArticleStatus::Published ? 'Jadikan draf' : 'Terbitkan' }}
                                            </flux:menu.item>

                                            <flux:menu.item wire:click="toggleFeatured({{ $article->id }})" icon="star">
                                                {{ $article->is_featured ? 'Hapus unggulan' : 'Tandai unggulan' }}
                                            </flux:menu.item>

                                            <flux:menu.separator />

                                            <flux:modal :name="'hapus-artikel-'.$article->id" class="min-w-[22rem] space-y-6">
                                                <div class="space-y-2">
                                                    <flux:heading level="2" size="lg">Hapus artikel?</flux:heading>

                                                    <flux:text>
                                                        “{{ $article->title }}” akan diarsipkan dan hilang dari situs.
                                                        Anda masih bisa memulihkannya lewat basis data.
                                                    </flux:text>
                                                </div>

                                                <div class="flex gap-2">
                                                    <flux:spacer />

                                                    <flux:modal.close>
                                                        <flux:button variant="ghost" type="button">Batal</flux:button>
                                                    </flux:modal.close>

                                                    <flux:button
                                                        variant="danger"
                                                        type="button"
                                                        icon="trash"
                                                        wire:click="destroy({{ $article->id }})"
                                                    >
                                                        Hapus
                                                    </flux:button>
                                                </div>
                                            </flux:modal>

                                            <flux:modal.trigger :name="'hapus-artikel-'.$article->id">
                                                <flux:menu.item variant="danger" icon="trash">Hapus</flux:menu.item>
                                            </flux:modal.trigger>
                                        </flux:menu>
                                    </flux:dropdown>
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            </div>

            <flux:pagination :paginator="$this->articles" class="mt-4" />
        </flux:card>
    @endif
</div>
