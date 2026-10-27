<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Faq;
use App\Models\Project;
use App\Models\Service;
use App\Models\Testimonial;
use App\Support\JsonLd;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;

class HomeController extends Controller
{
    /**
     * The landing page: hero, services, proof, portfolio, articles, FAQ.
     */
    public function __invoke(): View
    {
        $services = Service::query()->active()->ordered()->limit(6)->get();

        $projects = Project::query()->active()->ordered()->limit(6)->get();

        $articles = Article::query()->published()->ordered()->with(['author', 'category', 'tags'])->limit(3)->get();

        $faqs = Faq::query()->active()->ordered()->limit(6)->get();

        /** @var Collection<int, Testimonial> $testimonials */
        $testimonials = Testimonial::query()->active()->ordered()->limit(9)->get();

        return view('site.home', [
            'services' => $services,
            'projects' => $projects,
            'articles' => $articles,
            'faqs' => $faqs,
            'testimonials' => $testimonials,
            'schema' => array_values(array_filter([
                JsonLd::aggregateRating(
                    (float) $testimonials->avg('rating') ?: 5,
                    $testimonials->count()
                ),
                $faqs->isNotEmpty() ? JsonLd::faqPage($faqs) : null,
            ])),
        ]);
    }
}
