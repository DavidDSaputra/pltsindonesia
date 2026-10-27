<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\Project;
use App\Models\Service;
use App\Support\JsonLd;
use Illuminate\Contracts\View\View;

class ServiceController extends Controller
{
    /**
     * Overview of every active service offering.
     */
    public function index(): View
    {
        $services = Service::query()->active()->ordered()->get();

        if ($services->isEmpty()) {
            return view('site.empty', ['title' => 'Layanan']);
        }

        return view('site.services.index', [
            'services' => $services,
            'projects' => Project::query()->active()->ordered()->limit(3)->get(),
            'schema' => [
                JsonLd::breadcrumbs([
                    ['label' => 'Beranda', 'url' => route('home')],
                    ['label' => 'Layanan'],
                ]),
            ],
        ]);
    }

    /**
     * A single service, with matching projects and questions.
     */
    public function show(Service $service): View
    {
        abort_unless($service->is_active, 404);

        $faqs = Faq::query()->active()->where('group', $service->slug)->ordered()->get();

        if ($faqs->isEmpty()) {
            $faqs = Faq::query()->active()->ordered()->limit(5)->get();
        }

        return view('site.services.show', [
            'service' => $service,
            'others' => Service::query()->active()->ordered()->whereKeyNot($service->getKey())->limit(4)->get(),
            'projects' => Project::query()->active()->ordered()->limit(3)->get(),
            'faqs' => $faqs,
            'schema' => array_values(array_filter([
                JsonLd::service($service),
                JsonLd::breadcrumbs([
                    ['label' => 'Beranda', 'url' => route('home')],
                    ['label' => 'Layanan', 'url' => route('services.index')],
                    ['label' => $service->title],
                ]),
                $faqs->isNotEmpty() ? JsonLd::faqPage($faqs) : null,
            ])),
        ]);
    }
}
