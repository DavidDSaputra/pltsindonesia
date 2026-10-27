<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Support\Xml;
use DOMDocument;
use DOMElement;
use Illuminate\Http\Response;

class FeedController extends Controller
{
    /**
     * RSS 2.0 feed of the latest published articles.
     *
     * The document is assembled with DOMDocument rather than Blade so the XML
     * declaration cannot collide with PHP's open-tag parsing, and so text nodes
     * are escaped exactly once.
     */
    public function __invoke(): Response
    {
        $articles = Article::query()
            ->published()
            ->ordered()
            ->with(['author', 'category', 'tags'])
            ->limit(20)
            ->get();

        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;

        $rss = $dom->createElement('rss');
        $rss->setAttribute('version', '2.0');
        $rss->setAttribute('xmlns:atom', 'http://www.w3.org/2005/Atom');
        $dom->appendChild($rss);

        $channel = $dom->createElement('channel');
        $rss->appendChild($channel);

        $title = site('company_name', config('app.name')).' — Blog Energi Surya';
        $description = (string) site(
            'feed_description',
            'Artikel edukasi seputar panel surya, PLTS atap, dan penghematan listrik.'
        );

        $channel->appendChild(Xml::element($dom, 'title', $title));
        $channel->appendChild(Xml::element($dom, 'link', route('blog.index')));
        $channel->appendChild(Xml::element($dom, 'description', $description));
        $channel->appendChild(Xml::element($dom, 'language', 'id'));
        $channel->appendChild(Xml::element(
            $dom,
            'lastBuildDate',
            ($articles->first()?->updated_at ?? now())->toRfc2822String()
        ));

        $self = $dom->createElement('atom:link');
        $self->setAttribute('href', route('feed'));
        $self->setAttribute('rel', 'self');
        $self->setAttribute('type', 'application/rss+xml');
        $channel->appendChild($self);

        foreach ($articles as $article) {
            $channel->appendChild($this->item($dom, $article));
        }

        return response((string) $dom->saveXML(), 200, [
            'Content-Type' => 'application/rss+xml; charset=UTF-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    private function item(DOMDocument $dom, Article $article): DOMElement
    {
        $item = $dom->createElement('item');

        $item->appendChild(Xml::element($dom, 'title', $article->title));
        $item->appendChild(Xml::element($dom, 'link', route('blog.show', $article)));

        $guid = Xml::element($dom, 'guid', route('blog.show', $article));
        $guid->setAttribute('isPermaLink', 'true');
        $item->appendChild($guid);

        // Free-form copy goes into CDATA so markdown remnants never break the feed.
        $description = $dom->createElement('description');
        $description->appendChild($dom->createCDATASection($article->meta_description));
        $item->appendChild($description);

        $item->appendChild(Xml::element(
            $dom,
            'pubDate',
            ($article->published_at ?? $article->created_at)->toRfc2822String()
        ));

        if ($article->category) {
            $item->appendChild(Xml::element($dom, 'category', $article->category->name));
        }

        foreach ($article->tags as $tag) {
            $item->appendChild(Xml::element($dom, 'category', $tag->name));
        }

        $item->appendChild(Xml::element(
            $dom,
            'author',
            $article->author?->name ?? (string) site('company_name', config('app.name'))
        ));

        return $item;
    }
}
