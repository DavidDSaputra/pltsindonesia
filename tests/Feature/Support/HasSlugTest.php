<?php

namespace Tests\Feature\Support;

use App\Models\Article;
use App\Models\Category;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HasSlugTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_slug_is_derived_from_the_title_when_left_blank(): void
    {
        $service = Service::factory()->create(['title' => 'PLTS Atap On-Grid', 'slug' => null]);

        $this->assertSame('plts-atap-on-grid', $service->slug);
    }

    public function test_a_slug_is_derived_from_the_name_for_taxonomy_models(): void
    {
        $category = Category::factory()->create(['name' => 'Biaya & Investasi', 'slug' => null]);

        $this->assertSame('biaya-investasi', $category->slug);
    }

    public function test_colliding_slugs_get_a_numeric_suffix(): void
    {
        Service::factory()->create(['title' => 'Perawatan Sistem', 'slug' => 'perawatan-sistem']);
        $second = Service::factory()->create(['title' => 'Perawatan Sistem', 'slug' => null]);
        $third = Service::factory()->create(['title' => 'Perawatan Sistem', 'slug' => null]);

        $this->assertSame('perawatan-sistem-2', $second->slug);
        $this->assertSame('perawatan-sistem-3', $third->slug);
    }

    public function test_an_explicit_slug_is_never_overwritten(): void
    {
        $article = Article::factory()->create([
            'title' => 'Judul Baru Sepenuhnya',
            'slug' => 'judul-lama-yang-sudah-terindeks',
        ]);

        $article->update(['title' => 'Judul Diganti Lagi']);

        $this->assertSame('judul-lama-yang-sudah-terindeks', $article->fresh()->slug);
    }

    public function test_a_blank_source_falls_back_to_a_random_slug(): void
    {
        $service = Service::factory()->create(['title' => '###', 'slug' => null]);

        $this->assertNotSame('', $service->slug);
        $this->assertMatchesRegularExpression('/^[a-z0-9]{10}$/', $service->slug);
    }

    public function test_regenerate_slug_rebuilds_from_a_given_value(): void
    {
        $article = Article::factory()->create(['slug' => 'slug-lama']);

        $article->regenerateSlug('Judul Yang Diperbarui')->save();

        $this->assertSame('judul-yang-diperbarui', $article->fresh()->slug);
    }

    public function test_a_soft_deleted_slug_still_blocks_a_collision(): void
    {
        $original = Article::factory()->create(['slug' => 'artikel-dihapus']);
        $original->delete();

        $replacement = Article::factory()->create(['title' => 'Artikel Dihapus', 'slug' => null]);

        $this->assertSame('artikel-dihapus-2', $replacement->slug);
    }
}
