<?php

use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Models\Category;
use App\Models\Media;
use App\Models\Tag;
use Flux\Flux;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

new #[Title('Artikel')] class extends Component {
    use WithFileUploads;

    /**
     * Route middleware already guards HTTP requests; this keeps direct
     * component renders (e.g. Livewire::test) authorised as well.
     */
    public function boot(): void
    {
        abort_unless(Gate::allows('manage-content'), 403);
    }

    public ?int $articleId = null;

    public string $title = '';

    public string $slug = '';

    public bool $slugTouched = false;

    public string $category_id = '';

    public string $tagList = '';

    public string $excerpt = '';

    public string $body = '';

    public string $cover_image = '';

    /** @var TemporaryUploadedFile|null */
    public $coverUpload = null;

    public string $status = 'draft';

    public string $published_at = '';

    public bool $is_featured = false;

    public string $seo_title = '';

    public string $seo_description = '';

    public string $seo_keywords = '';

    public bool $previewing = false;

    /**
     * Validation rules shared by the create and edit flows.
     *
     * @return array<string, mixed>
     */
    protected function rulesFor(): array
    {
        return [
            'title' => ['required', 'string', 'max:190'],
            'slug' => [
                'nullable', 'string', 'max:200', 'alpha_dash',
                Rule::unique('articles', 'slug')->ignore($this->articleId),
            ],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'tagList' => ['nullable', 'string', 'max:400'],
            'excerpt' => ['nullable', 'string', 'max:320'],
            'body' => ['required', 'string', 'min:40'],
            'cover_image' => ['nullable', 'string', 'max:500'],
            'coverUpload' => ['nullable', 'image', 'max:4096'],
            'status' => ['required', 'in:draft,published'],
            'published_at' => ['nullable', 'date'],
            'seo_title' => ['nullable', 'string', 'max:70'],
            'seo_description' => ['nullable', 'string', 'max:170'],
            'seo_keywords' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'title.required' => 'Judul artikel wajib diisi.',
            'body.required' => 'Isi artikel wajib diisi.',
            'body.min' => 'Isi artikel minimal 40 karakter.',
            'slug.alpha_dash' => 'Slug hanya boleh berisi huruf, angka, tanda hubung, dan garis bawah.',
            'slug.unique' => 'Slug ini sudah dipakai artikel lain.',
            'coverUpload.image' => 'Berkas sampul harus berupa gambar.',
            'coverUpload.max' => 'Ukuran gambar maksimal 4 MB.',
            'seo_title.max' => 'Judul SEO sebaiknya tidak lebih dari 70 karakter.',
            'seo_description.max' => 'Deskripsi SEO sebaiknya tidak lebih dari 170 karakter.',
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function validationAttributes(): array
    {
        return [
            'title' => 'judul',
            'slug' => 'slug',
            'category_id' => 'kategori',
            'tagList' => 'daftar tag',
            'excerpt' => 'ringkasan',
            'body' => 'isi artikel',
            'cover_image' => 'gambar sampul',
            'coverUpload' => 'berkas sampul',
            'status' => 'status',
            'published_at' => 'tanggal terbit',
            'seo_title' => 'judul SEO',
            'seo_description' => 'deskripsi SEO',
            'seo_keywords' => 'kata kunci SEO',
        ];
    }

    public function mount(?Article $article = null): void
    {
        if ($article === null) {
            return;
        }

        $this->articleId = $article->id;
        $this->title = (string) $article->title;
        $this->slug = (string) $article->slug;
        $this->slugTouched = true;
        $this->category_id = $article->category_id !== null ? (string) $article->category_id : '';
        $this->tagList = $article->tags()->ordered()->pluck('name')->implode(', ');
        $this->excerpt = (string) $article->excerpt;
        $this->body = (string) $article->body;
        $this->cover_image = (string) $article->cover_image;
        $this->status = $article->status->value;
        $this->published_at = $article->published_at?->format('Y-m-d\TH:i') ?? '';
        $this->is_featured = (bool) $article->is_featured;
        $this->seo_title = (string) $article->seo_title;
        $this->seo_description = (string) $article->seo_description;
        $this->seo_keywords = (string) $article->seo_keywords;
    }

    /**
     * Suggest a slug from the title until the editor writes one by hand.
     */
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

    /**
     * Keep the excerpt in sync while the body is being written.
     */
    public function updatedBody(): void
    {
        if ($this->excerpt === '') {
            $this->excerpt = Str::limit(trim(strip_tags($this->body)), 160, '');
        }
    }

    public function updatedCoverUpload(): void
    {
        $this->validate(['coverUpload' => ['nullable', 'image', 'max:4096']], $this->messages(), $this->validationAttributes());
    }

    public function removeCover(): void
    {
        $this->coverUpload = null;
        $this->cover_image = '';
    }

    #[Computed]
    public function coverPreview(): ?string
    {
        if ($this->coverUpload instanceof TemporaryUploadedFile) {
            try {
                return $this->coverUpload->temporaryUrl();
            } catch (\Throwable) {
                return null;
            }
        }

        return image_url($this->cover_image ?: null);
    }

    #[Computed]
    public function bodyHtml(): string
    {
        return Str::markdown($this->body !== '' ? $this->body : '_Belum ada isi artikel._');
    }

    #[Computed]
    public function readingTime(): int
    {
        return max(1, (int) ceil(str_word_count(strip_tags($this->body)) / Article::READING_SPEED));
    }

    #[Computed]
    public function categories()
    {
        return Category::query()->ordered()->get();
    }

    /**
     * Create or update the article, then return to the list.
     */
    public function save(): void
    {
        $data = $this->validate($this->rulesFor(), $this->messages(), $this->validationAttributes());

        $cover = $this->cover_image;

        if ($this->coverUpload instanceof TemporaryUploadedFile) {
            $cover = Media::storeUpload($this->coverUpload, Auth::id(), 'articles')->path;
        }

        unset($data['coverUpload'], $data['tagList']);

        $data['cover_image'] = filled($cover) ? $cover : null;
        $data['slug'] = filled($data['slug']) ? $data['slug'] : null;
        $data['published_at'] = filled($data['published_at']) ? Carbon::parse($data['published_at']) : null;
        $data['status'] = ArticleStatus::from($data['status']);
        $data['category_id'] = filled($data['category_id'] ?? null) ? (int) $data['category_id'] : null;
        $data['is_featured'] = $this->is_featured;

        $article = $this->articleId
            ? Article::query()->findOrFail($this->articleId)
            : new Article(['user_id' => Auth::id()]);

        $article->fill($data);
        $article->save();

        $article->tags()->sync($this->syncTags());

        $this->coverUpload = null;
        $this->articleId = $article->id;

        Flux::toast(
            variant: 'success',
            text: $article->wasRecentlyCreated ? 'Artikel berhasil dibuat.' : 'Perubahan artikel disimpan.',
        );

        $this->redirectRoute('admin.articles.index', navigate: true);
    }

    /**
     * Resolve the comma-separated tag input into tag ids, creating new tags.
     *
     * @return array<int, int>
     */
    protected function syncTags(): array
    {
        $ids = [];

        foreach (explode(',', $this->tagList) as $name) {
            $name = trim($name);
            $slug = Str::slug($name);

            if ($slug === '') {
                continue;
            }

            $tag = Tag::query()->firstOrNew(['slug' => $slug]);

            if (! $tag->exists) {
                $tag->name = $name;
                $tag->save();
            }

            $ids[] = $tag->id;
        }

        return array_values(array_unique($ids));
    }

    /**
     * Permanently remove the article.
     */
    public function destroy(): void
    {
        abort_unless($this->articleId !== null, 404);

        $article = Article::withTrashed()->findOrFail($this->articleId);

        $article->forceDelete();

        Flux::toast(variant: 'success', text: 'Artikel dihapus permanen.');

        $this->redirectRoute('admin.articles.index', navigate: true);
    }
}; ?>

<div class="space-y-6">
    <x-admin.page-header
        :title="$articleId ? 'Ubah Artikel' : 'Tulis Artikel Baru'"
        description="Tulis isi dalam Markdown. Judul, sampul, dan kolom SEO menentukan tampilan artikel di mesin pencari."
    >
        <flux:button :href="route('admin.articles.index')" icon="arrow-left" wire:navigate>
            Kembali
        </flux:button>
    </x-admin.page-header>

    <form wire:submit="save" class="grid items-start gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
            <flux:card class="space-y-5">
                <flux:input
                    wire:model.blur="title"
                    label="Judul artikel"
                    placeholder="Contoh: Cara Menghitung Kebutuhan Panel Surya Rumah 900 VA"
                    type="text"
                    required
                    autofocus
                />

                <flux:input
                    wire:model.blur="slug"
                    label="Slug"
                    description="Bagian akhir URL: /blog/{slug}. Kosongkan untuk dibuat otomatis dari judul."
                    placeholder="cara-menghitung-kebutuhan-panel-surya"
                    type="text"
                />

                <flux:textarea
                    wire:model.blur="excerpt"
                    label="Ringkasan"
                    description="Muncul di kartu artikel dan hasil pencarian. Kosongkan untuk diambil otomatis dari isi."
                    placeholder="Ringkasan singkat 1–2 kalimat."
                    rows="3"
                />

                <div class="space-y-2">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <flux:label for="isi-artikel">Isi artikel (Markdown)</flux:label>

                        <div class="flex items-center gap-2">
                            <flux:text size="sm">{{ $this->readingTime }} menit baca</flux:text>

                            <flux:button
                                size="xs"
                                variant="ghost"
                                type="button"
                                icon="eye"
                                wire:click="$toggle('previewing')"
                            >
                                {{ $previewing ? 'Sembunyikan pratinjau' : 'Pratinjau' }}
                            </flux:button>
                        </div>
                    </div>

                    <flux:textarea
                        id="isi-artikel"
                        wire:model.blur="body"
                        rows="18"
                        placeholder="## Sub judul&#10;&#10;Tulis paragraf di sini. Gunakan **tebal**, *miring*, daftar, dan tautan [teks](https://…)."
                    />

                    <flux:error name="body" />

                    <p class="text-xs text-zinc-500 dark:text-zinc-400">
                        Mendukung judul <code>##</code>, daftar <code>-</code>, tabel, kutipan <code>&gt;</code>,
                        gambar <code>![alt](url)</code>, dan tautan <code>[teks](url)</code>.
                    </p>
                </div>

                @if ($previewing)
                    <div
                        class="prose prose-zinc max-w-none rounded-xl border border-zinc-200 bg-zinc-50/60 p-5 dark:prose-invert dark:border-zinc-700 dark:bg-zinc-900/40"
                        aria-live="polite"
                    >
                        {!! $this->bodyHtml !!}
                    </div>
                @endif
            </flux:card>

            <flux:card class="space-y-4">
                <div>
                    <flux:heading level="2" size="lg">SEO</flux:heading>

                    <flux:text size="sm">
                        Kosongkan untuk memakai judul dan ringkasan artikel secara otomatis.
                    </flux:text>
                </div>

                <flux:input
                    wire:model.blur="seo_title"
                    label="Judul SEO"
                    description="Maksimal 70 karakter agar tidak terpotong di hasil pencarian."
                    type="text"
                    :placeholder="$title ?: 'Judul untuk mesin pencari'"
                />

                <flux:textarea
                    wire:model.blur="seo_description"
                    label="Deskripsi SEO"
                    description="Maksimal 170 karakter. Dipakai untuk meta description dan cuplikan media sosial."
                    rows="3"
                    :placeholder="$excerpt ?: 'Deskripsi singkat untuk mesin pencari'"
                />

                <flux:input
                    wire:model.blur="seo_keywords"
                    label="Kata kunci"
                    description="Pisahkan dengan koma. Bersifat pelengkap, bukan faktor peringkat utama."
                    placeholder="panel surya, PLTS atap, harga solar panel"
                    type="text"
                />
            </flux:card>
        </div>

        <div class="space-y-6">
            <flux:card class="space-y-5">
                <flux:select wire:model.blur="status" label="Status">
                    @foreach (ArticleStatus::cases() as $option)
                        <flux:select.option value="{{ $option->value }}">{{ $option->label() }}</flux:select.option>
                    @endforeach
                </flux:select>

                <flux:input
                    wire:model.blur="published_at"
                    label="Tanggal terbit"
                    description="Kosongkan untuk memakai waktu penyimpanan saat status “Terbit”."
                    type="datetime-local"
                />

                <flux:select wire:model.blur="category_id" label="Kategori" placeholder="Tanpa kategori">
                    @foreach ($this->categories as $category)
                        <flux:select.option value="{{ $category->id }}">{{ $category->name }}</flux:select.option>
                    @endforeach
                </flux:select>

                <flux:input
                    wire:model.blur="tagList"
                    label="Tag"
                    description="Pisahkan dengan koma. Tag baru dibuat otomatis."
                    placeholder="panel surya, hemat listrik"
                    type="text"
                />

                <flux:switch wire:model="is_featured" label="Tampilkan sebagai artikel unggulan" />
            </flux:card>

            <flux:card class="space-y-4">
                <x-admin.image-field
                    label="Gambar sampul"
                    model="coverUpload"
                    path-model="cover_image"
                    :preview="$this->coverPreview"
                    description="Rasio 16:9 tampil paling baik. Contoh: articles/panel-surya.jpg"
                />

                @if ($this->coverPreview)
                    <flux:button
                        type="button"
                        size="sm"
                        variant="danger"
                        icon="trash"
                        wire:click="removeCover"
                    >
                        Hapus sampul
                    </flux:button>
                @endif
            </flux:card>

            <div class="sticky bottom-4 flex flex-wrap items-center gap-2 rounded-xl border border-zinc-200 bg-white/95 p-3 backdrop-blur dark:border-zinc-700 dark:bg-zinc-800/95">
                <flux:button variant="primary" type="submit" icon="check" wire:loading.attr="disabled">
                    {{ $articleId ? 'Simpan Perubahan' : 'Simpan Artikel' }}
                </flux:button>

                <flux:button type="button" variant="ghost" :href="route('admin.articles.index')" wire:navigate>
                    Batal
                </flux:button>

                <div class="ms-auto flex items-center gap-2">
                    @if ($articleId && filled($slug))
                        <flux:button
                            type="button"
                            size="sm"
                            variant="ghost"
                            icon="arrow-top-right-on-square"
                            :href="route('blog.show', $slug)"
                            target="_blank"
                        >
                            Lihat
                        </flux:button>
                    @endif

                    @if ($articleId)
                        <flux:modal name="hapus-artikel" class="min-w-[22rem] space-y-6">
                            <div class="space-y-2">
                                <flux:heading level="2" size="lg">Hapus artikel?</flux:heading>

                                <flux:text>
                                    Tindakan ini menghapus artikel secara permanen dan tidak dapat dibatalkan.
                                </flux:text>
                            </div>

                            <div class="flex gap-2">
                                <flux:spacer />

                                <flux:modal.close>
                                    <flux:button variant="ghost" type="button">Batal</flux:button>
                                </flux:modal.close>

                                <flux:button variant="danger" type="button" icon="trash" wire:click="destroy">
                                    Hapus Permanen
                                </flux:button>
                            </div>
                        </flux:modal>

                        <flux:modal.trigger name="hapus-artikel">
                            <flux:button type="button" size="sm" variant="ghost" icon="trash" class="text-red-500">
                                Hapus
                            </flux:button>
                        </flux:modal.trigger>
                    @endif
                </div>
            </div>
        </div>
    </form>
</div>
