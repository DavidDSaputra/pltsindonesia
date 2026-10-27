@php
    $company = site('company_name', config('app.name'));
    $whatsapp = site('whatsapp');
    $ctaUrl = filled($whatsapp) ? 'https://wa.me/'.preg_replace('/\D+/', '', $whatsapp) : route('contact');

    $navItems = [
        ['label' => 'Beranda', 'href' => route('home'), 'active' => 'home'],
        ['label' => 'Tentang Kami', 'href' => route('about'), 'active' => 'about'],
        ['label' => 'Layanan', 'href' => route('services.index'), 'active' => 'services.*'],
        ['label' => 'Proyek', 'href' => route('projects.index'), 'active' => 'projects.*'],
        ['label' => 'Galeri', 'href' => route('gallery'), 'active' => 'gallery'],
        ['label' => 'Blog', 'href' => route('blog.index'), 'active' => 'blog.*'],
        ['label' => 'FAQ', 'href' => route('faq'), 'active' => 'faq'],
        ['label' => 'Kontak', 'href' => route('contact'), 'active' => 'contact'],
    ];

    $menuColumns = [array_slice($navItems, 0, 4), array_slice($navItems, 4, 4)];
@endphp

<header
    aria-label="Header utama {{ $company }}"
    x-data="{ menu: false }"
    x-init="$watch('menu', value => document.body.classList.toggle('brix-menu-lock', value))"
    @keydown.escape.window="menu = false"
    class="brix-navigation"
>
    <div class="brix-nav-backdrop" :class="menu ? 'is-open' : ''" @click="menu = false" aria-hidden="true"></div>

    <div class="brix-nav-shell" :class="menu ? 'is-open' : ''">
        <div class="brix-nav-topbar">
            <button
                type="button"
                class="brix-nav-burger"
                @click="menu = ! menu"
                :aria-expanded="menu ? 'true' : 'false'"
                aria-controls="brix-nav-panel"
            >
                <span class="sr-only" x-text="menu ? 'Tutup menu' : 'Buka menu'"></span>
                <span class="brix-nav-burger-lines" aria-hidden="true">
                    <span class="brix-nav-line brix-nav-line-one"></span>
                    <span class="brix-nav-line brix-nav-line-two"></span>
                </span>
            </button>

            <a href="{{ route('home') }}" class="brix-nav-logo" aria-label="{{ $company }} — beranda">
                <img
                    src="/images/logo-navbar.png"
                    alt="{{ $company }}"
                    class="brix-nav-logo-image"
                    width="48"
                    height="48"
                    fetchpriority="high"
                    decoding="async"
                />
            </a>

            <a
                href="{{ $ctaUrl }}"
                @if (filled($whatsapp)) target="_blank" rel="noopener noreferrer" @endif
                class="brix-nav-start"
            >Start</a>
        </div>

        <div id="brix-nav-panel" class="brix-nav-panel" :aria-hidden="menu ? 'false' : 'true'">
            <div class="brix-nav-menu-content">
                <nav class="brix-nav-menu-links" aria-label="Navigasi utama">
                    @foreach ($menuColumns as $columnIndex => $column)
                        <div class="brix-nav-menu-column">
                            @foreach ($column as $itemIndex => $item)
                                <a
                                    href="{{ $item['href'] }}"
                                    @if (request()->routeIs($item['active'])) aria-current="page" @endif
                                    class="brix-nav-menu-link {{ request()->routeIs($item['active']) ? 'is-current' : '' }}"
                                    style="--menu-index: {{ $columnIndex * 3 + $itemIndex }}"
                                    @click="menu = false"
                                >
                                    <span class="brix-nav-menu-text"><span>{{ $item['label'] }}</span><span aria-hidden="true">{{ $item['label'] }}</span></span>
                                    <x-site.icon name="arrow-up-right" class="brix-nav-menu-arrow" stroke="1.8" />
                                </a>
                            @endforeach
                        </div>
                    @endforeach
                </nav>

                <a href="{{ route('projects.index') }}" class="brix-nav-image-box" @click="menu = false">
                    <img
                        src="/images/photos/panels.jpg"
                        alt="Panel surya untuk proyek energi bersih"
                        class="brix-nav-image"
                        width="1600"
                        height="1060"
                        loading="lazy"
                        decoding="async"
                    />
                    <span class="brix-nav-image-caption">Solusi energi surya <span aria-hidden="true">↗</span></span>
                </a>
            </div>
        </div>
    </div>
</header>
