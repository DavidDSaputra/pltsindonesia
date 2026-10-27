@php
    $uid = 'hi-'.\Illuminate\Support\Str::lower(\Illuminate\Support\Str::random(6));
@endphp

<svg {{ $attributes }} viewBox="0 0 300 640" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
    <defs>
        <linearGradient id="{{ $uid }}-body" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0" stop-color="#f7f9f8" />
            <stop offset="1" stop-color="#bfc7c3" />
        </linearGradient>
        <linearGradient id="{{ $uid }}-steel" x1="0" y1="0" x2="1" y2="0">
            <stop offset="0" stop-color="#8d979a" />
            <stop offset="0.45" stop-color="#e6eaeb" />
            <stop offset="1" stop-color="#7a8487" />
        </linearGradient>
    </defs>

    {{-- Mounting frame --}}
    <rect x="62" y="70" width="14" height="570" fill="url(#{{ $uid }}-steel)" />
    <rect x="224" y="70" width="14" height="570" fill="url(#{{ $uid }}-steel)" />
    <rect x="52" y="92" width="196" height="12" rx="3" fill="#6d7679" />
    <rect x="52" y="372" width="196" height="12" rx="3" fill="#6d7679" />

    {{-- Cables running down into the ground --}}
    <path d="M118 384c0 70-14 110-18 256" stroke="#2a312f" stroke-width="9" stroke-linecap="round" />
    <path d="M150 384c0 80 6 130 6 256" stroke="#3a4341" stroke-width="9" stroke-linecap="round" />
    <path d="M182 384c0 70 14 110 18 256" stroke="#2a312f" stroke-width="9" stroke-linecap="round" />

    {{-- Enclosure --}}
    <rect x="34" y="110" width="232" height="266" rx="16" fill="url(#{{ $uid }}-body)" stroke="#aeb7b3" stroke-width="2" />
    <rect x="34" y="110" width="232" height="30" rx="16" fill="#12372a" />
    <text x="150" y="130" text-anchor="middle" font-family="Arial, sans-serif" font-size="11" font-weight="700" letter-spacing="3" fill="#e7b83c">PLTSINDONESIA</text>

    <rect x="56" y="156" width="188" height="118" rx="9" fill="#1a2420" />
    <rect x="72" y="172" width="156" height="58" rx="5" fill="#0b1411" />
    <text x="150" y="212" text-anchor="middle" font-family="'Courier New', monospace" font-size="26" font-weight="700" fill="#7dffb4">12.4 kW</text>
    <circle cx="96" cy="252" r="6" fill="#4ade80" />
    <circle cx="124" cy="252" r="6" fill="#e7b83c" />
    <circle cx="152" cy="252" r="6" fill="#4b5753" />
    <rect x="184" y="244" width="44" height="16" rx="4" fill="#3a4642" />

    {{-- Vents --}}
    <g stroke="#9aa49f" stroke-width="3" stroke-linecap="round">
        <path d="M60 296h180M60 312h180M60 328h180M60 344h180" />
    </g>

    {{-- Ground plate --}}
    <path d="M30 636h240l10 4H20l10-4Z" fill="#9aa09b" />
</svg>
