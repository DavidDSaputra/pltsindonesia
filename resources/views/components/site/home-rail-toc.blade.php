@props(['items'])

<nav class="home-rail-toc" aria-label="Jelajahi bagian halaman" data-home-rail>
    <p class="home-rail-toc-title">
        <svg viewBox="0 0 16 16" fill="none" aria-hidden="true">
            <path d="M2 4h12M2 8h8M2 12h10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
        </svg>
        On this page
    </p>

    <div class="home-rail-toc-body">
        <svg class="home-rail-svg" data-rail-svg aria-hidden="true">
            <path class="home-rail-path home-rail-path--dashed" data-rail-path></path>
            <path class="home-rail-path home-rail-path--filled" data-rail-fill></path>
        </svg>

        <span class="home-rail-plane" data-rail-plane aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" focusable="false">
                <path d="m21.2 3.2-7.5 17.4-3.2-7-7.2-3 17.9-7.4Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round" />
                <path d="m10.5 13.6 3.3-3.2" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" />
            </svg>
        </span>

        <div class="home-rail-toc-list">
            @foreach ($items as $item)
                <a
                    href="#{{ $item['id'] }}"
                    class="home-rail-toc-link"
                    data-rail-link
                    data-rail-target="{{ $item['id'] }}"
                    style="--rail-depth: {{ $item['depth'] ?? 0 }}"
                >
                    <span class="home-rail-toc-dot" aria-hidden="true"></span>
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach
        </div>
    </div>
</nav>
