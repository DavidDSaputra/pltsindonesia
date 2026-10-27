<?php

namespace Tests\Feature\Site;

use App\Models\Article;
use App\Models\Category;
use App\Models\Faq;
use App\Models\Gallery;
use App\Models\Project;
use App\Models\Service;
use App\Models\Tag;
use App\Models\Testimonial;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_renders_with_content_blocks(): void
    {
        Service::factory()->count(3)->create();
        Project::factory()->count(3)->create(['is_featured' => true]);
        Testimonial::factory()->count(2)->create();
        Article::factory()->count(3)->create();

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('id="konten"', false);
        $response->assertSee('class="skip-link"', false);
        $response->assertSee('lang="id"', false);
    }

    public function test_home_page_renders_without_any_content(): void
    {
        $this->get('/')->assertOk();
    }

    public function test_static_pages_render(): void
    {
        foreach (['/tentang-kami', '/galeri', '/faq', '/kontak', '/kebijakan-privasi', '/syarat-ketentuan'] as $path) {
            $this->get($path)->assertOk();
        }
    }

    public function test_faq_page_lists_only_active_questions(): void
    {
        $active = Faq::factory()->create(['is_active' => true, 'group' => 'biaya']);
        $hidden = Faq::factory()->create(['is_active' => false, 'group' => 'biaya']);

        $response = $this->get('/faq');

        $response->assertOk();
        $response->assertSee($active->question);
        $response->assertDontSee($hidden->question);
    }

    public function test_gallery_page_lists_only_active_images(): void
    {
        $shown = Gallery::factory()->create(['is_active' => true, 'title' => 'Instalasi Atap Gudang']);
        $hidden = Gallery::factory()->create(['is_active' => false, 'title' => 'Foto Arsip Internal']);

        $response = $this->get('/galeri');

        $response->assertOk();
        $response->assertSee($shown->title);
        $response->assertDontSee($hidden->title);
    }

    public function test_service_index_lists_active_services_only(): void
    {
        $active = Service::factory()->create(['title' => 'PLTS Atap On-Grid']);
        $hidden = Service::factory()->inactive()->create(['title' => 'Layanan Tidak Aktif']);

        $response = $this->get('/layanan');

        $response->assertOk();
        $response->assertSee($active->title);
        $response->assertDontSee($hidden->title);
    }

    public function test_service_detail_renders(): void
    {
        $service = Service::factory()->create(['title' => 'Sistem Hybrid Baterai']);

        $this->get('/layanan/'.$service->slug)
            ->assertOk()
            ->assertSee($service->title);
    }

    public function test_inactive_service_detail_is_not_found(): void
    {
        $service = Service::factory()->inactive()->create();

        $this->get('/layanan/'.$service->slug)->assertNotFound();
    }

    public function test_project_index_and_detail_render(): void
    {
        $project = Project::factory()->create(['title' => 'Gudang Logistik Cikarang']);

        $this->get('/proyek')->assertOk()->assertSee($project->title);
        $this->get('/proyek/'.$project->slug)->assertOk()->assertSee($project->title);
    }

    public function test_inactive_project_detail_is_not_found(): void
    {
        $project = Project::factory()->inactive()->create();

        $this->get('/proyek/'.$project->slug)->assertNotFound();
    }

    public function test_blog_index_hides_drafts_and_scheduled_articles(): void
    {
        $live = Article::factory()->create(['title' => 'Artikel Sudah Terbit']);
        $draft = Article::factory()->draft()->create(['title' => 'Artikel Masih Draf']);
        $scheduled = Article::factory()->create([
            'title' => 'Artikel Terjadwal',
            'published_at' => now()->addWeek(),
        ]);

        $response = $this->get('/blog');

        $response->assertOk();
        $response->assertSee($live->title);
        $response->assertDontSee($draft->title);
        $response->assertDontSee($scheduled->title);
    }

    public function test_article_detail_renders_and_counts_a_view(): void
    {
        $article = Article::factory()->create(['views_count' => 10]);

        $this->get('/blog/'.$article->slug)->assertOk()->assertSee($article->title);

        $this->assertSame(11, $article->fresh()->views_count);
    }

    public function test_draft_article_detail_is_not_found(): void
    {
        $article = Article::factory()->draft()->create();

        $this->get('/blog/'.$article->slug)->assertNotFound();
    }

    public function test_category_and_tag_pages_render(): void
    {
        $category = Category::factory()->create(['name' => 'Regulasi']);
        $tag = Tag::factory()->create(['name' => 'On-Grid']);

        $article = Article::factory()->create(['category_id' => $category->id]);
        $article->tags()->attach($tag);

        $this->get('/blog/kategori/'.$category->slug)->assertOk()->assertSee($article->title);
        $this->get('/blog/tag/'.$tag->slug)->assertOk()->assertSee($article->title);
    }

    public function test_blog_search_narrows_the_listing(): void
    {
        Article::factory()->create(['title' => 'Memilih Inverter Hybrid']);
        $other = Article::factory()->create(['title' => 'Perawatan Modul Surya']);

        $response = $this->get('/blog?cari=inverter');

        $response->assertOk();
        $response->assertSee('Memilih Inverter Hybrid');
        $response->assertDontSee($other->title);
    }

    public function test_unknown_slugs_return_not_found(): void
    {
        $this->get('/layanan/tidak-ada')->assertNotFound();
        $this->get('/proyek/tidak-ada')->assertNotFound();
        $this->get('/blog/tidak-ada')->assertNotFound();
        $this->get('/halaman-yang-hilang')->assertNotFound();
    }

    public function test_the_not_found_page_renders_in_indonesian(): void
    {
        $response = $this->get('/halaman-yang-hilang');

        $response->assertStatus(404);
        $response->assertSee('Halaman tidak ditemukan', false);
    }

    public function test_pages_carry_a_canonical_link(): void
    {
        $this->get('/')->assertSee('<link rel="canonical" href="'.url('/').'" />', false);
    }
}
