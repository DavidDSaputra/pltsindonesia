<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Support\JsonLd;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Portfolio index with optional client/location filtering.
     */
    public function index(Request $request): View
    {
        if (! Project::query()->active()->exists()) {
            return view('site.empty', ['title' => 'Proyek']);
        }

        $projects = Project::query()
            ->active()
            ->ordered()
            ->when(
                filled($request->string('filter')->toString()),
                fn ($query) => $query->where('location', 'like', '%'.$request->string('filter')->toString().'%')
            )
            ->paginate(9)
            ->withQueryString();

        return view('site.projects.index', [
            'projects' => $projects,
            'locations' => Project::query()->active()->distinct()->orderBy('location')->pluck('location')->filter()->values(),
            'schema' => [
                JsonLd::breadcrumbs([
                    ['label' => 'Beranda', 'url' => route('home')],
                    ['label' => 'Proyek'],
                ]),
            ],
        ]);
    }

    /**
     * A single project case study.
     */
    public function show(Project $project): View
    {
        abort_unless($project->is_active, 404);

        return view('site.projects.show', [
            'project' => $project,
            'related' => Project::query()
                ->active()
                ->ordered()
                ->whereKeyNot($project->getKey())
                ->limit(3)
                ->get(),
            'schema' => array_values(array_filter([
                JsonLd::breadcrumbs([
                    ['label' => 'Beranda', 'url' => route('home')],
                    ['label' => 'Proyek', 'url' => route('projects.index')],
                    ['label' => $project->title],
                ]),
                JsonLd::clean([
                    '@context' => 'https://schema.org',
                    '@type' => 'Article',
                    'headline' => $project->title,
                    'description' => $project->meta_description,
                    'image' => $project->image_url ? [url($project->image_url)] : null,
                    'datePublished' => $project->completed_on?->toIso8601String(),
                    'about' => JsonLd::clean([
                        '@type' => 'Place',
                        'name' => $project->location,
                        'address' => $project->location,
                    ]),
                    'author' => ['@id' => url('/').'#organization'],
                    'publisher' => ['@id' => url('/').'#organization'],
                ]),
            ])),
        ]);
    }
}
