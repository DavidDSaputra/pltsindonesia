<?php

namespace App\Support;

use App\Models\Article;
use App\Models\Faq;
use App\Models\Service;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * Builds schema.org JSON-LD payloads for the public site.
 *
 * Everything is derived from CMS settings/content, and empty values are stripped
 * so Google never receives null placeholders.
 */
class JsonLd
{
    /**
     * The organisation behind the site (also used as LocalBusiness when an address exists).
     *
     * @return array<string, mixed>
     */
    public static function organization(): array
    {
        $hasAddress = filled(site('address')) || filled(site('city'));

        $socials = array_values(array_filter((array) site('social', []), fn ($url) => filled($url)));

        return self::clean([
            '@context' => 'https://schema.org',
            '@type' => $hasAddress ? ['Organization', 'LocalBusiness'] : 'Organization',
            '@id' => url('/').'#organization',
            'name' => site('company_name', config('app.name')),
            'legalName' => site('company_legal_name'),
            'alternateName' => site('company_short_name'),
            'url' => url('/'),
            'description' => site('company_description'),
            'logo' => self::clean([
                '@type' => 'ImageObject',
                'url' => url('/images/logo-navbar.png'),
            ]),
            'image' => filled(site('og_image')) ? url((string) site('og_image')) : null,
            'email' => site('email'),
            'telephone' => site('phone'),
            'foundingDate' => site('founded_year'),
            'address' => $hasAddress ? self::clean([
                '@type' => 'PostalAddress',
                'streetAddress' => site('address'),
                'addressLocality' => site('city'),
                'addressRegion' => site('region'),
                'postalCode' => site('postal_code'),
                'addressCountry' => site('country', 'ID'),
            ]) : null,
            'geo' => (filled(site('geo_lat')) && filled(site('geo_lng'))) ? [
                '@type' => 'GeoCoordinates',
                'latitude' => site('geo_lat'),
                'longitude' => site('geo_lng'),
            ] : null,
            'openingHours' => site('business_hours'),
            'sameAs' => $socials !== [] ? $socials : null,
            'contactPoint' => self::clean([
                '@type' => 'ContactPoint',
                'contactType' => 'customer service',
                'telephone' => site('phone'),
                'email' => site('email'),
                'areaServed' => site('service_area', 'ID'),
                'availableLanguage' => ['id', 'en'],
            ]),
        ]);
    }

    /**
     * WebSite entity with a sitelinks search box pointing at the blog search.
     *
     * @return array<string, mixed>
     */
    public static function website(): array
    {
        return self::clean([
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            '@id' => url('/').'#website',
            'url' => url('/'),
            'name' => site('company_name', config('app.name')),
            'publisher' => ['@id' => url('/').'#organization'],
            'inLanguage' => str_replace('_', '-', app()->getLocale()),
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => [
                    '@type' => 'EntryPoint',
                    'urlTemplate' => route('blog.index').'?cari={search_term_string}',
                ],
                'query-input' => 'required name=search_term_string',
            ],
        ]);
    }

    /**
     * Breadcrumb trail matching the visible breadcrumbs component.
     *
     * @param  array<int, array{label: string, url?: string|null}>  $items
     * @return array<string, mixed>
     */
    public static function breadcrumbs(array $items): array
    {
        $position = 0;

        $elements = collect($items)
            ->map(function (array $item) use (&$position) {
                $position++;

                return self::clean([
                    '@type' => 'ListItem',
                    'position' => $position,
                    'name' => $item['label'],
                    'item' => isset($item['url']) ? url($item['url']) : null,
                ]);
            })
            ->all();

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $elements,
        ];
    }

    /**
     * Article (NewsArticle/BlogPosting) entity for a single blog post.
     *
     * @return array<string, mixed>
     */
    public static function article(Article $article): array
    {
        return self::clean([
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            '@id' => route('blog.show', $article).'#article',
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => route('blog.show', $article),
            ],
            'headline' => Str::limit($article->title, 110, ''),
            'description' => $article->meta_description,
            'image' => $article->cover_url ? [url($article->cover_url)] : null,
            'datePublished' => $article->published_at?->toIso8601String(),
            'dateModified' => $article->updated_at?->toIso8601String(),
            'articleSection' => $article->category?->name,
            'keywords' => filled($article->seo_keywords) ? $article->seo_keywords : $article->tags->pluck('name')->implode(', '),
            'inLanguage' => 'id',
            'wordCount' => str_word_count(strip_tags($article->body)),
            'author' => self::clean([
                '@type' => 'Person',
                'name' => $article->author?->name ?? site('company_name', config('app.name')),
            ]),
            'publisher' => ['@id' => url('/').'#organization'],
        ]);
    }

    /**
     * FAQPage entity built from the visible FAQ entries.
     *
     * @param  Collection<int, Faq>|array<int, Faq>  $faqs
     * @return array<string, mixed>
     */
    public static function faqPage(Collection|array $faqs): array
    {
        $items = collect($faqs)
            ->map(fn (Faq $faq) => [
                '@type' => 'Question',
                'name' => $faq->question,
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $faq->answer,
                ],
            ])
            ->values()
            ->all();

        return [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'inLanguage' => 'id',
            'mainEntity' => $items,
        ];
    }

    /**
     * Service entity for a service detail page.
     *
     * @return array<string, mixed>
     */
    public static function service(Service $service): array
    {
        return self::clean([
            '@context' => 'https://schema.org',
            '@type' => 'Service',
            '@id' => route('services.show', $service).'#service',
            'name' => $service->title,
            'description' => $service->meta_description,
            'url' => route('services.show', $service),
            'serviceType' => $service->title,
            'areaServed' => self::clean([
                '@type' => 'Country',
                'name' => site('service_area_name', 'Indonesia'),
            ]),
            'provider' => ['@id' => url('/').'#organization'],
            'image' => $service->image_url ? [url($service->image_url)] : null,
        ]);
    }

    /**
     * Aggregate rating block for the testimonials carousel.
     *
     * @return array<string, mixed>|null
     */
    public static function aggregateRating(float $rating, int $count): ?array
    {
        if ($count === 0) {
            return null;
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            '@id' => url('/').'#organization',
            'aggregateRating' => [
                '@type' => 'AggregateRating',
                'ratingValue' => number_format($rating, 1, '.', ''),
                'reviewCount' => $count,
                'bestRating' => '5',
                'worstRating' => '1',
            ],
        ];
    }

    /**
     * Recursively drop null and empty-array values so the payload stays valid.
     *
     * @param  array<array-key, mixed>  $data
     * @return array<array-key, mixed>
     */
    public static function clean(array $data): array
    {
        return Arr::where(
            array_map(
                fn ($value) => is_array($value) ? self::clean($value) : $value,
                $data
            ),
            fn ($value) => ! is_array($value) ? filled($value) : $value !== []
        );
    }
}
