@props([
    'testimonials',
    'tone' => 'light',
])

@php
    /** @var \Illuminate\Support\Collection<int, \App\Models\Testimonial> $testimonials */
    $dark = $tone !== 'light';
@endphp

@if ($testimonials->isNotEmpty())
    {{-- Reference-style review wall: a calm two-column grid of bordered cards. --}}
    <ul class="grid gap-5 sm:grid-cols-2" role="list" aria-label="Ulasan pelanggan">
        @foreach ($testimonials as $testimonial)
            <li class="reveal" style="--reveal-delay: {{ ($loop->index % 2) * 90 }}ms">
                <figure
                    @class([
                        'flex h-full flex-col gap-5 rounded-[10px] border p-6',
                        'border-hairline bg-white' => ! $dark,
                        'border-white/15 bg-white/5' => $dark,
                    ])
                >
                    <div class="flex items-center gap-1" role="img" aria-label="Rating {{ $testimonial->rating }} dari 5">
                        @for ($star = 1; $star <= 5; $star++)
                            <x-site.icon
                                name="star"
                                class="size-4"
                                :stroke="1.6"
                                @class([
                                    'text-tan' => $star <= $testimonial->rating,
                                    'text-hairline' => $star > $testimonial->rating && ! $dark,
                                    'text-white/25' => $star > $testimonial->rating && $dark,
                                ])
                            />
                        @endfor
                    </div>

                    <blockquote class="flex-1">
                        <p @class([
                            'text-sm leading-relaxed sm:text-base',
                            'text-ink' => ! $dark,
                            'text-white/85' => $dark,
                        ])>
                            &ldquo;{{ $testimonial->quote }}&rdquo;
                        </p>
                    </blockquote>

                    <figcaption class="flex items-center gap-3 border-t pt-4 @class([
                        'border-hairline' => ! $dark,
                        'border-white/15' => $dark,
                    ])">
                        <span
                            @class([
                                'grid size-10 shrink-0 place-items-center rounded-full text-sm font-bold',
                                'bg-coal text-solar-300' => ! $dark,
                                'bg-solar-400 text-night-950' => $dark,
                            ])
                            aria-hidden="true"
                        >
                            {{ \Illuminate\Support\Str::of($testimonial->name)->substr(0, 1)->upper() }}
                        </span>

                        <span class="min-w-0">
                            <span @class([
                                'block truncate text-sm font-semibold',
                                'text-ink' => ! $dark,
                                'text-white' => $dark,
                            ])>{{ $testimonial->name }}</span>

                            <span @class([
                                'block truncate text-xs',
                                'text-ink/60' => ! $dark,
                                'text-white/60' => $dark,
                            ])>{{ $testimonial->attribution }}</span>
                        </span>
                    </figcaption>
                </figure>
            </li>
        @endforeach
    </ul>
@endif
