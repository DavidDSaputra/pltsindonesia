<?php

namespace Tests\Feature\Site;

use App\Models\Article;
use App\Models\Category;
use App\Models\Project;
use App\Models\Service;
use App\Models\Tag;
use DOMDocument;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    public function test_robots_txt_blocks_the_cms_and_points_at_the_sitemap(): void
    {
        $response = $this->get('/robots.txt');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/plain; charset=UTF-8');

        $body = $response->getContent();

        $this->assertStringContainsString('User-agent: *', $body);
        $this->assertStringContainsString('Disallow: /admin', $body);
        $this->assertStringContainsString('Disallow: /dashboard', $body);
        $this->assertStringContainsString('Sitemap: '.url('/sitemap.xml'), $body);
    }

    public function test_the_sitemap_is_well_formed_and_lists_indexable_urls(): void
    {
        $service = Service::factory()->create();
        $project = Project::factory()->create();
        $article = Article::factory()->create();
        $category = Category::factory()->create();
        $tag = Tag::factory()->create();
        $article->tags()->attach($tag);

        $response = $this->get('/sitemap.xml');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/xml; charset=UTF-8');

        $xml = $this->parseXml($response->getContent());
        $locs = collect($xml->getElementsByTagName('loc'))
            ->map(fn (\DOMElement $node) => $node->textContent)
            ->all();

        $this->assertContains(url('/'), $locs);
        $this->assertContains(route('services.show', $service), $locs);
        $this->assertContains(route('projects.show', $project), $locs);
        $this->assertContains(route('blog.show', $article), $locs);
        $this->assertContains(route('blog.category', $category), $locs);
        $this->assertContains(route('blog.tag', $tag), $locs);
    }

    public function test_the_sitemap_omits_drafts_and_inactive_records(): void
    {
        $draft = Article::factory()->draft()->create();
        $hidden = Service::factory()->inactive()->create();
        $archived = Project::factory()->inactive()->create();

        $locs = $this->sitemapLocations();

        $this->assertNotContains(route('blog.show', $draft), $locs);
        $this->assertNotContains(route('services.show', $hidden), $locs);
        $this->assertNotContains(route('projects.show', $archived), $locs);
    }

    public function test_the_feed_is_well_formed_rss(): void
    {
        $article = Article::factory()->create();

        $response = $this->get('/feed.xml');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/rss+xml; charset=UTF-8');

        $xml = $this->parseXml($response->getContent());

        $this->assertSame('2.0', $xml->documentElement->getAttribute('version'));
        $this->assertSame('id', $xml->getElementsByTagName('language')->item(0)?->textContent);
        $this->assertSame(
            $article->title,
            $xml->getElementsByTagName('item')->item(0)?->getElementsByTagName('title')->item(0)?->textContent
        );
    }

    /**
     * Ampersands are common in Indonesian copy. DOMDocument::createElement()
     * drops such values entirely, so this guards the text-node fix.
     */
    public function test_the_feed_escapes_ampersands_in_titles_and_categories(): void
    {
        $category = Category::factory()->create(['name' => 'Biaya & Investasi']);
        Article::factory()->create([
            'title' => 'Syarat & Ketentuan PLTS Atap',
            'category_id' => $category->id,
        ]);

        $xml = $this->parseXml($this->get('/feed.xml')->getContent());

        $item = $xml->getElementsByTagName('item')->item(0);

        $this->assertSame(
            'Syarat & Ketentuan PLTS Atap',
            $item?->getElementsByTagName('title')->item(0)?->textContent
        );

        $categories = collect($item?->getElementsByTagName('category') ?? [])
            ->map(fn (\DOMElement $node) => $node->textContent)
            ->all();

        $this->assertContains('Biaya & Investasi', $categories);
    }

    public function test_the_feed_omits_drafts(): void
    {
        $draft = Article::factory()->draft()->create(['title' => 'Artikel Masih Draf']);

        $this->assertStringNotContainsString(
            'Artikel Masih Draf',
            $this->get('/feed.xml')->getContent()
        );

        $this->assertNotNull($draft);
    }

    public function test_the_feed_link_is_advertised_when_rss_is_enabled(): void
    {
        $this->get('/')->assertSee('type="application/rss+xml"', false);
    }

    public function test_pages_advertise_an_indonesian_locale_and_open_graph_image(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('<html lang="id"', false);
        $response->assertSee('<meta property="og:locale" content="id_ID" />', false);
        $response->assertSee('<meta property="og:image" content="', false);
        $response->assertSee('<meta name="twitter:card" content="summary_large_image" />', false);
    }

    public function test_pages_expose_organization_structured_data(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('<script type="application/ld+json">', false);

        $blocks = $this->jsonLdBlocks($response->getContent());

        $this->assertContains('Organization', collect($blocks)->pluck('@type')->all());
    }

    public function test_an_article_page_exposes_article_structured_data(): void
    {
        $article = Article::factory()->create();

        $response = $this->get('/blog/'.$article->slug);

        $response->assertOk();

        $blocks = $this->jsonLdBlocks($response->getContent());

        $this->assertContains('BlogPosting', collect($blocks)->pluck('@type')->all());
        $this->assertContains('BreadcrumbList', collect($blocks)->pluck('@type')->all());
    }

    /**
     * Every public page keeps a single top-level heading for screen readers.
     */
    public function test_each_public_page_has_exactly_one_h1(): void
    {
        $service = Service::factory()->create();
        $project = Project::factory()->create();
        $article = Article::factory()->create();

        $pages = [
            '/',
            '/tentang-kami',
            '/layanan',
            '/layanan/'.$service->slug,
            '/proyek',
            '/proyek/'.$project->slug,
            '/galeri',
            '/blog',
            '/blog/'.$article->slug,
            '/faq',
            '/kontak',
            '/kebijakan-privasi',
            '/syarat-ketentuan',
        ];

        foreach ($pages as $page) {
            $html = $this->get($page)->assertOk()->getContent();

            $this->assertSame(
                1,
                preg_match_all('/<h1[\s>]/', $html),
                "Expected exactly one <h1> on {$page}"
            );
        }
    }

    public function test_the_404_page_asks_crawlers_not_to_index_it(): void
    {
        $this->get('/halaman-yang-hilang')
            ->assertStatus(404)
            ->assertSee('noindex', false);
    }

    /**
     * @return array<int, string>
     */
    protected function sitemapLocations(): array
    {
        $xml = $this->parseXml($this->get('/sitemap.xml')->getContent());

        return collect($xml->getElementsByTagName('loc'))
            ->map(fn (\DOMElement $node) => $node->textContent)
            ->all();
    }

    protected function parseXml(string $contents): DOMDocument
    {
        $dom = new DOMDocument;

        $previous = libxml_use_internal_errors(true);
        $loaded = $dom->loadXML($contents);
        $errors = libxml_get_errors();
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $this->assertTrue(
            $loaded && $errors === [],
            'Response is not well-formed XML: '.implode('; ', array_map(
                fn ($error) => trim($error->message).' (line '.$error->line.')',
                $errors
            ))
        );

        return $dom;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function jsonLdBlocks(string $html): array
    {
        preg_match_all(
            '#<script type="application/ld\+json">(.*?)</script>#s',
            $html,
            $matches
        );

        return collect($matches[1])
            ->map(fn (string $json) => json_decode($json, true))
            ->filter(fn ($decoded) => is_array($decoded))
            ->values()
            ->all();
    }
}
