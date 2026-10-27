@php
    use Illuminate\Support\Collection;

    /** @var Collection<int, \App\Models\Gallery> $images */

    $crumbs = [
        ['label' => 'Beranda', 'url' => route('home')],
        ['label' => 'Galeri'],
    ];

    $groupNames = collect($groups)->filter()->values();

    // Cycle portrait / square / landscape frames so the masonry reads organic.
    $ratios = ['aspect-[4/3]', 'aspect-square', 'aspect-[3/4]', 'aspect-[16/10]'];
@endphp

<x-site.layout
    :title="site('gallery_meta_title', 'Galeri Instalasi Panel Surya')"
    :description="site('gallery_meta_description', 'Dokumentasi foto pemasangan panel surya, inverter, dan struktur rangka pada berbagai jenis atap.')"
    :schema="$schema ?? []"
    :canonical="route('gallery')"
>
    <x-site.page-hero
        :crumbs="$crumbs"
        eyebrow="Galeri"
        title="Dokumentasi lapangan"
        description="Foto-foto asli dari proses pemasangan hingga sistem siap beroperasi."
    />

    <x-site.section tone="dark" labelled-by="galeri-heading">
        <x-site.container>
            <x-site.section-heading
                id="galeri-heading"
                tone="dark"
                class="reveal"
                eyebrow="Album"
                eyebrow-icon="image"
                :title="$images->count().' foto instalasi'"
                description="Klik salah satu foto untuk melihatnya lebih besar."
            />

            @if ($images->isEmpty())
                <p class="mt-12 bg-white/5 p-10 text-center text-sm text-white/60">
                    Galeri sedang kami siapkan. Silakan kembali lagi nanti.
                </p>
            @else
                @if ($groupNames->isNotEmpty())
                    <div
                        x-data="{ group: '' }"
                        class="mt-10"
                    >
                        <div class="flex flex-wrap gap-2" role="group" aria-label="Saring album galeri">
                            <button
                                type="button"
                                @click="group = ''"
                                :aria-pressed="group === '' ? 'true' : 'false'"
                                :class="group === '' ? 'bg-solar-400 text-night-950' : 'bg-white/10 text-white/80 hover:bg-white/20'"
                                class="px-4 py-2 text-sm font-semibold transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-solar-400"
                            >
                                Semua
                            </button>

                            @foreach ($groupNames as $groupName)
                                <button
                                    type="button"
                                    @click="group = '{{ $groupName }}'"
                                    :aria-pressed="group === '{{ $groupName }}' ? 'true' : 'false'"
                                    :class="group === '{{ $groupName }}' ? 'bg-solar-400 text-night-950' : 'bg-white/10 text-white/80 hover:bg-white/20'"
                                    class="px-4 py-2 text-sm font-semibold transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-solar-400"
                                >
                                    {{ $groupName }}
                                </button>
                            @endforeach
                        </div>

                        <ul class="mt-8 columns-1 gap-5 sm:columns-2 lg:columns-3">
                            @foreach ($images as $image)
                                <li
                                    x-show="group === '' || group === '{{ $image->group }}'"
                                    data-group="{{ $image->group }}"
                                    class="reveal mb-5 break-inside-avoid"
                                    style="--reveal-delay: {{ ($loop->index % 3) * 90 }}ms"
                                >
                                    <figure class="group/figure">
                                        <button
                                            type="button"
                                            class="block w-full cursor-zoom-in text-left focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-solar-400"
                                            data-dialog-target="dialog-galeri-{{ $image->id }}"
                                            onclick="document.getElementById('dialog-galeri-{{ $image->id }}').showModal()"
                                            aria-haspopup="dialog"
                                        >
                                            <x-site.image
                                                :src="$image->image"
                                                :alt="$image->alt_text"
                                                variant="panels"
                                                :ratio="$ratios[$loop->index % count($ratios)]"
                                            />
                                        </button>

                                        @if (filled($image->title))
                                            <figcaption class="flex items-center justify-between gap-3 py-3">
                                                <span class="truncate text-sm text-white/80">{{ $image->title }}</span>

                                                @if (filled($image->group))
                                                    <span class="shrink-0 text-[11px] tracking-[0.18em] text-white/50 uppercase">
                                                        {{ $image->group }}
                                                    </span>
                                                @endif
                                            </figcaption>
                                        @endif
                                    </figure>

                                    {{-- Native dialog: focus trapping, Esc to close and aria-modal are handled by the browser. --}}
                                    <dialog
                                        id="dialog-galeri-{{ $image->id }}"
                                        class="w-full max-w-4xl bg-transparent p-0 backdrop:bg-coal/90 backdrop:backdrop-blur-sm"
                                        aria-label="{{ $image->title ?: 'Pratinjau foto' }}"
                                    >
                                        <div class="bg-white shadow-2xl">
                                            <div class="flex items-start justify-between gap-4 border-b border-hairline px-5 py-4">
                                                <div class="min-w-0">
                                                    <p class="truncate text-sm font-bold text-ink">{{ $image->title ?: 'Foto instalasi' }}</p>

                                                    @if (filled($image->group))
                                                        <p class="mt-0.5 text-xs text-ink/60">{{ $image->group }}</p>
                                                    @endif
                                                </div>

                                                <button
                                                    type="button"
                                                    class="grid size-9 shrink-0 place-items-center text-ink transition-colors hover:bg-tile focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-solar-600"
                                                    onclick="document.getElementById('dialog-galeri-{{ $image->id }}').close()"
                                                >
                                                    <span class="sr-only">Tutup pratinjau</span>
                                                    <x-site.icon name="close" class="size-5" stroke="2" />
                                                </button>
                                            </div>

                                            <x-site.image
                                                :src="$image->image"
                                                :alt="$image->alt_text"
                                                variant="sunset"
                                                ratio="aspect-[16/10]"
                                                eager
                                            />

                                            @if (filled($image->alt) && $image->alt !== $image->title)
                                                <p class="px-5 py-4 text-sm leading-relaxed text-ink/70">{{ $image->alt }}</p>
                                            @endif
                                        </div>
                                    </dialog>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    <ul class="mt-10 columns-1 gap-5 sm:columns-2 lg:columns-3">
                        @foreach ($images as $image)
                            <li class="reveal mb-5 break-inside-avoid" style="--reveal-delay: {{ ($loop->index % 3) * 90 }}ms">
                                <figure>
                                    <x-site.image
                                        :src="$image->image"
                                        :alt="$image->alt_text"
                                        variant="panels"
                                        :ratio="$ratios[$loop->index % count($ratios)]"
                                    />

                                    @if (filled($image->title))
                                        <figcaption class="py-3 text-sm text-white/80">{{ $image->title }}</figcaption>
                                    @endif
                                </figure>
                            </li>
                        @endforeach
                    </ul>
                @endif
            @endif
        </x-site.container>
    </x-site.section>

    <x-site.cta-banner
        title="Ingin melihat langsung?"
        description="Kami bisa aturkan kunjungan ke proyek yang sudah beroperasi di kota Anda, selama diizinkan pemiliknya."
    />
</x-site.layout>
