<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use App\Models\Project;
use App\Models\Service;
use App\Models\Tag;
use App\Support\Xml;
use Carbon\CarbonInterface;
use DOMDocument;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;

class SitemapController extends Controller
{
    /**
     * XML sitemap covering every indexable public URL.
     */
    public function __invoke(): Response
    {
        $urls = collect([
            $this->entry(route('home'), Carbon::now()->startOfDay(), 'daily', '1.0'),
            $this->entry(route('about'), null, 'monthly', '0.8'),
            $this->entry(route('services.index'), null, 'weekly', '0.9'),
            $this->entry(route('projects.index'), null, 'weekly', '0.8'),
            $this->entry(route('gallery'), null, 'monthly', '0.6'),
            $this->entry(route('blog.index'), Carbon::now()->startOfDay(), 'daily', '0.9'),
            $this->entry(route('faq'), null, 'monthly', '0.6'),
            $this->entry(route('contact'), null, 'monthly', '0.7'),
            $this->entry(route('privacy'), null, 'yearly', '0.3'),
            $this->entry(route('terms'), null, 'yearly', '0.3'),
        ]);

        Service::query()->active()->orderBy('sort_order')->get(['slug', 'updated_at'])
            ->each(fn (Service $service) => $urls->push(
                $this->entry(route('services.show', $service), $service->updated_at, 'monthly', '0.8')
            ));

        Project::query()->active()->orderByDesc('completed_on')->get(['slug', 'updated_at'])
            ->each(fn (Project $project) => $urls->push(
                $this->entry(route('projects.show', $project), $project->updated_at, 'monthly', '0.7')
            ));

        Article::query()->published()->ordered()->get(['slug', 'updated_at'])
            ->each(fn (Article $article) => $urls->push(
                $this->entry(route('blog.show', $article), $article->updated_at, 'weekly', '0.7')
            ));

        Category::query()->ordered()->get(['slug', 'updated_at'])
            ->each(fn (Category $category) => $urls->push(
                $this->entry(route('blog.category', $category), $category->updated_at, 'weekly', '0.5')
            ));

        Tag::query()->ordered()->get(['slug', 'updated_at'])
            ->each(fn (Tag $tag) => $urls->push(
                $this->entry(route('blog.tag', $tag), $tag->updated_at, 'monthly', '0.4')
            ));

        $xml = $this->render($urls->all());

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    /**
     * Build the XML document without going through Blade, so the
     * declaration cannot be mistaken for a PHP open tag.
     *
     * @param  array<int, array{loc: string, lastmod: string|null, changefreq: string, priority: string}>  $entries
     */
    private function render(array $entries): string
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;

        $urlset = $dom->createElement('urlset');
        $urlset->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $dom->appendChild($urlset);

        foreach ($entries as $entry) {
            $url = $dom->createElement('url');

            foreach (['loc', 'lastmod', 'changefreq', 'priority'] as $key) {
                if (filled($entry[$key])) {
                    // A text node escapes the value exactly once.
                    $url->appendChild(Xml::element($dom, $key, (string) $entry[$key]));
                }
            }

            $urlset->appendChild($url);
        }

        return (string) $dom->saveXML();
    }

    /**
     * @return array{loc: string, lastmod: string|null, changefreq: string, priority: string}
     */
    private function entry(string $loc, ?CarbonInterface $lastmod, string $changefreq, string $priority): array
    {
        return [
            'loc' => $loc,
            'lastmod' => $lastmod?->toAtomString(),
            'changefreq' => $changefreq,
            'priority' => $priority,
        ];
    }
}
