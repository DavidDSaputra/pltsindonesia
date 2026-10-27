@props([
    'title',
    'eyebrow' => null,
    'description' => null,
    'crumbs' => [],
])

{{-- Compact editorial banner shared by every inner page: paper band, hairline
     breadcrumbs and an oversized display headline. --}}
<section class="border-b border-hairline bg-paper text-ink">
    <x-site.container>
        <div class="grid gap-6 py-14 sm:py-16 lg:py-20">
            @if (count($crumbs) > 0)
                <x-site.breadcrumbs :items="$crumbs" tone="light" />
            @endif

            <div class="max-w-3xl">
                @if ($eyebrow)
                    <x-site.eyebrow icon="sun" class="reveal">{{ $eyebrow }}</x-site.eyebrow>
                @endif

                <h1
                    class="reveal mt-4 font-display text-4xl tracking-tight text-balance text-ink sm:text-5xl lg:text-6xl"
                    style="--reveal-delay: 80ms"
                >
                    {{ $title }}
                </h1>

                @if ($description)
                    <p class="reveal mt-4 text-base leading-relaxed text-ink/70 sm:text-lg" style="--reveal-delay: 160ms">
                        {{ $description }}
                    </p>
                @endif
            </div>

            @if (trim($slot) !== '')
                <div class="flex flex-wrap items-center gap-3">
                    {{ $slot }}
                </div>
            @endif
        </div>
    </x-site.container>
</section>
