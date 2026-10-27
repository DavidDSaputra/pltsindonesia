<?php

namespace Tests\Feature\Admin;

use App\Enums\ArticleStatus;
use App\Enums\UserRole;
use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ArticleCrudTest extends TestCase
{
    use RefreshDatabase;

    protected User $author;

    protected function setUp(): void
    {
        parent::setUp();

        $this->author = User::factory()->admin()->create();
    }

    /**
     * Body copy has to clear the 40-character minimum, so tests use a real paragraph.
     */
    protected function body(): string
    {
        return "## Gambaran umum\n\nSistem PLTS atap mengubah sinar matahari menjadi listrik yang langsung dipakai oleh beban rumah.";
    }

    public function test_the_create_screen_starts_empty(): void
    {
        Livewire::actingAs($this->author)
            ->test('pages::admin.articles.form')
            ->assertSet('articleId', null)
            ->assertSet('title', '')
            ->assertSet('status', 'draft');
    }

    public function test_a_slug_is_suggested_from_the_title(): void
    {
        Livewire::actingAs($this->author)
            ->test('pages::admin.articles.form')
            ->set('title', 'Cara Kerja PLTS On-Grid')
            ->assertSet('slug', 'cara-kerja-plts-on-grid');
    }

    public function test_a_hand_written_slug_is_kept(): void
    {
        Livewire::actingAs($this->author)
            ->test('pages::admin.articles.form')
            ->set('slug', 'slug-khusus-saya')
            ->set('title', 'Judul Yang Berbeda')
            ->assertSet('slug', 'slug-khusus-saya');
    }

    public function test_the_excerpt_follows_the_body_until_it_is_edited(): void
    {
        Livewire::actingAs($this->author)
            ->test('pages::admin.articles.form')
            ->set('body', $this->body())
            ->assertSet('excerpt', function (?string $excerpt) {
                return filled($excerpt) && str_starts_with($excerpt, '## Gambaran umum');
            });
    }

    public function test_an_editor_can_publish_a_new_article(): void
    {
        $category = Category::factory()->create();

        Livewire::actingAs($this->author)
            ->test('pages::admin.articles.form')
            ->set([
                'title' => 'Memilih Inverter yang Tepat',
                'slug' => 'memilih-inverter-yang-tepat',
                'category_id' => (string) $category->id,
                'body' => $this->body(),
                'status' => 'published',
                'is_featured' => true,
                'seo_title' => 'Panduan Memilih Inverter PLTS',
                'seo_description' => 'Perbandingan inverter string, micro-inverter, dan hybrid untuk atap di Indonesia.',
            ])
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.articles.index'));

        $article = Article::query()->where('slug', 'memilih-inverter-yang-tepat')->firstOrFail();

        $this->assertSame(ArticleStatus::Published, $article->status);
        $this->assertTrue($article->is_featured);
        $this->assertSame($category->id, $article->category_id);
        $this->assertSame($this->author->id, $article->user_id);
        $this->assertNotNull($article->published_at);
        $this->assertFalse($article->published_at->isFuture());
    }

    public function test_a_saved_draft_stays_unpublished(): void
    {
        Livewire::actingAs($this->author)
            ->test('pages::admin.articles.form')
            ->set(['title' => 'Catatan Internal', 'body' => $this->body(), 'status' => 'draft'])
            ->call('save')
            ->assertHasNoErrors();

        $draft = Article::query()->firstOrFail();

        $this->assertSame(ArticleStatus::Draft, $draft->status);
        $this->assertNull($draft->published_at);
        $this->assertFalse($draft->isPublished());
    }

    public function test_the_title_and_body_are_required(): void
    {
        Livewire::actingAs($this->author)
            ->test('pages::admin.articles.form')
            ->set(['title' => '', 'body' => 'terlalu pendek'])
            ->call('save')
            ->assertHasErrors(['title', 'body']);

        $this->assertDatabaseCount('articles', 0);
    }

    public function test_a_duplicate_slug_is_rejected_with_an_indonesian_message(): void
    {
        Article::factory()->create(['slug' => 'slug-sudah-dipakai']);

        Livewire::actingAs($this->author)
            ->test('pages::admin.articles.form')
            ->set(['title' => 'Judul Lain', 'slug' => 'slug-sudah-dipakai', 'body' => $this->body()])
            ->call('save')
            ->assertHasErrors('slug', 'unique');
    }

    public function test_seo_fields_respect_their_length_limits(): void
    {
        Livewire::actingAs($this->author)
            ->test('pages::admin.articles.form')
            ->set([
                'title' => 'Judul Artikel',
                'body' => $this->body(),
                'seo_title' => str_repeat('a', 71),
            ])
            ->call('save')
            ->assertHasErrors('seo_title', 'max');
    }

    public function test_tags_are_created_on_the_fly_and_attached(): void
    {
        Tag::factory()->create(['name' => 'On-Grid', 'slug' => 'on-grid']);

        Livewire::actingAs($this->author)
            ->test('pages::admin.articles.form')
            ->set([
                'title' => 'Artikel Bertag',
                'body' => $this->body(),
                'tagList' => 'On-Grid, Perawatan, on-grid',
            ])
            ->call('save')
            ->assertHasNoErrors();

        $article = Article::query()->firstOrFail();

        $this->assertSame(
            ['On-Grid', 'Perawatan'],
            $article->tags()->ordered()->pluck('name')->all()
        );
    }

    public function test_the_edit_screen_hydrates_from_the_article(): void
    {
        $article = Article::factory()->create([
            'title' => 'Artikel Lama',
            'slug' => 'artikel-lama',
            'status' => ArticleStatus::Published,
            'is_featured' => true,
        ]);

        Livewire::actingAs($this->author)
            ->test('pages::admin.articles.form', ['article' => $article])
            ->assertSet('articleId', $article->id)
            ->assertSet('title', 'Artikel Lama')
            ->assertSet('slug', 'artikel-lama')
            ->assertSet('status', 'published')
            ->assertSet('is_featured', true);
    }

    public function test_an_editor_can_update_an_existing_article(): void
    {
        $article = Article::factory()->create(['title' => 'Judul Sebelum Diubah']);

        Livewire::actingAs($this->author)
            ->test('pages::admin.articles.form', ['article' => $article])
            ->set('title', 'Judul Sesudah Diubah')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.articles.index'));

        $this->assertSame('Judul Sesudah Diubah', $article->fresh()->title);
        $this->assertDatabaseCount('articles', 1);
    }

    public function test_the_listing_toggles_status_and_featured_flag(): void
    {
        $article = Article::factory()->create(['status' => ArticleStatus::Draft, 'is_featured' => false]);

        Livewire::actingAs($this->author)
            ->test('pages::admin.articles.index')
            ->call('toggleStatus', $article->id)
            ->call('toggleFeatured', $article->id);

        $fresh = $article->fresh();

        $this->assertSame(ArticleStatus::Published, $fresh->status);
        $this->assertTrue($fresh->is_featured);
        $this->assertNotNull($fresh->published_at);
    }

    public function test_the_listing_soft_deletes_an_article(): void
    {
        $article = Article::factory()->create();

        Livewire::actingAs($this->author)
            ->test('pages::admin.articles.index')
            ->call('destroy', $article->id);

        $this->assertSoftDeleted('articles', ['id' => $article->id]);
        $this->assertSame(0, Article::query()->count());
    }

    public function test_the_listing_filters_by_status(): void
    {
        $live = Article::factory()->create(['title' => 'Artikel Terbit']);
        $draft = Article::factory()->draft()->create(['title' => 'Artikel Draf']);

        Livewire::actingAs($this->author)
            ->test('pages::admin.articles.index')
            ->set('status', 'draft')
            ->assertSee($draft->title)
            ->assertDontSee($live->title);
    }

    public function test_a_regular_user_cannot_reach_the_article_form(): void
    {
        $visitor = User::factory()->create(['role' => UserRole::User]);

        Livewire::actingAs($visitor)
            ->test('pages::admin.articles.form')
            ->assertForbidden();
    }
}
