@props([
    'service',
    'variant' => 'card',
])

@php
    /** @var \App\Models\Service $service */
    $features = collect((array) ($service->features ?? []))->filter()->values();
    $wide = $variant === 'wide';
@endphp

{{-- Editorial service card: sharp hairline frame, coal icon slab, display headline. --}}
<article
    @class([
        'group relative flex flex-col border border-hairline bg-white transition-colors hover:border-ink/30',
        'sm:flex-row' => $wide,
    ])
>
    @if ($wide)
        <div class="relative w-full shrink-0 overflow-hidden sm:w-56">
            <x-site.image
                :src="$service->image"
                :alt="'Ilustrasi layanan '.$service->title"
                variant="panels"
                ratio="aspect-[16/10] sm:aspect-auto sm:size-full"
            />
        </div>
    @endif

    <div class="flex flex-1 flex-col p-6">
        <span class="grid size-12 place-items-center bg-coal text-solar-400" aria-hidden="true">
            <x-site.icon :name="$service->icon ?: 'panels'" class="size-6" stroke="1.8" />
        </span>

        <h3 @class([
            'mt-5 font-display tracking-tight text-ink',
            'text-xl' => ! $wide,
            'text-2xl' => $wide,
        ])>
            <a href="{{ route('services.show', $service) }}" class="before:absolute before:inset-0 before:content-[''] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-solar-600">
                {{ $service->title }}
            </a>
        </h3>

        <p class="mt-2.5 text-sm leading-relaxed text-ink/70">
            {{ $service->summary }}
        </p>

        @if ($features->isNotEmpty())
            <ul class="mt-4 grid gap-2 border-t border-hairline pt-4">
                @foreach ($features->take($wide ? 6 : 3) as $feature)
                    <li class="flex items-start gap-2 text-sm text-ink">
                        <x-site.icon name="check" class="mt-0.5 size-4 shrink-0 text-solar-600" stroke="2.4" />
                        <span>{{ $feature }}</span>
                    </li>
                @endforeach
            </ul>
        @endif

        <span class="mt-5 inline-flex items-center gap-1.5 pt-1 text-sm font-semibold text-solar-700" aria-hidden="true">
            Pelajari layanan
            <x-site.icon name="arrow-right" class="size-4 transition-transform group-hover:translate-x-0.5" stroke="2" />
        </span>
    </div>
</article>
