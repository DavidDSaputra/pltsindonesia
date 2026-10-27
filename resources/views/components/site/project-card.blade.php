@props([
    'project',
])

@php
    /** @var \App\Models\Project $project */
@endphp

<article class="group relative overflow-hidden bg-coal">
    <x-site.image
        :src="$project->image"
        :alt="'Foto proyek PLTS '.$project->title.($project->location ? ' di '.$project->location : '')"
        variant="panels"
        ratio="aspect-[4/3]"
    />

    <div class="absolute inset-0 bg-linear-to-t from-coal via-coal/45 to-transparent" aria-hidden="true"></div>

    <div class="absolute inset-x-0 bottom-0 p-5">
        @if ($project->capacity_label || $project->completed_on)
            <p class="mb-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-[11px] tracking-[0.18em] text-solar-300 uppercase">
                @if ($project->capacity_label)
                    <span>{{ $project->capacity_label }}</span>
                @endif

                @if ($project->completed_on)
                    <time datetime="{{ $project->completed_on->toDateString() }}">
                        {{ $project->completed_on->translatedFormat('F Y') }}
                    </time>
                @endif
            </p>
        @endif

        <h3 class="font-display text-xl tracking-tight text-white">
            <a href="{{ route('projects.show', $project) }}" class="before:absolute before:inset-0 before:content-[''] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-solar-400">
                {{ $project->title }}
            </a>
        </h3>

        <p class="mt-1.5 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-white/70">
            @if (filled($project->location))
                <span class="inline-flex items-center gap-1.5">
                    <x-site.icon name="pin" class="size-3.5 shrink-0" stroke="2" />
                    {{ $project->location }}
                </span>
            @endif

            @if (filled($project->client))
                <span class="inline-flex items-center gap-1.5">
                    <x-site.icon name="building" class="size-3.5 shrink-0" stroke="2" />
                    {{ $project->client }}
                </span>
            @endif
        </p>
    </div>
</article>
