@props(['cols' => 6, 'rows' => 4])

@php
    // Corners of the module face (slightly keystoned so it reads as a tilted array on a pole).
    $tl = [30, 70]; $tr = [390, 22]; $br = [388, 332]; $bl = [34, 392];

    $lerp = fn (array $a, array $b, float $t): array => [$a[0] + ($b[0] - $a[0]) * $t, $a[1] + ($b[1] - $a[1]) * $t];
    $uid = 'hp-'.\Illuminate\Support\Str::lower(\Illuminate\Support\Str::random(6));

    $vertical = [];
    for ($i = 1; $i < $cols; $i++) {
        $vertical[] = [$lerp($tl, $tr, $i / $cols), $lerp($bl, $br, $i / $cols)];
    }

    $horizontal = [];
    for ($j = 1; $j < $rows; $j++) {
        $horizontal[] = [$lerp($tl, $bl, $j / $rows), $lerp($tr, $br, $j / $rows)];
    }
@endphp

<svg {{ $attributes }} viewBox="0 0 420 780" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
    <defs>
        <linearGradient id="{{ $uid }}-glass" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0" stop-color="#27467a" />
            <stop offset="0.55" stop-color="#112447" />
            <stop offset="1" stop-color="#0a1830" />
        </linearGradient>
        <linearGradient id="{{ $uid }}-shine" x1="0" y1="0" x2="1" y2="0.6">
            <stop offset="0" stop-color="#fff" stop-opacity="0.34" />
            <stop offset="0.45" stop-color="#fff" stop-opacity="0.04" />
            <stop offset="1" stop-color="#fff" stop-opacity="0" />
        </linearGradient>
        <linearGradient id="{{ $uid }}-steel" x1="0" y1="0" x2="1" y2="0">
            <stop offset="0" stop-color="#8d979a" />
            <stop offset="0.45" stop-color="#e6eaeb" />
            <stop offset="1" stop-color="#7a8487" />
        </linearGradient>
    </defs>

    {{-- Pole, brace and concrete footing --}}
    <rect x="198" y="330" width="24" height="430" fill="url(#{{ $uid }}-steel)" />
    <path d="M210 520 120 600M210 520 300 600" stroke="#6d7679" stroke-width="7" stroke-linecap="round" />
    <path d="M112 758h196l14 18H98l14-18Z" fill="#9aa09b" />
    <path d="M112 758h196" stroke="#c5cac5" stroke-width="3" />

    {{-- Rear rail --}}
    <path d="M60 372 360 318" stroke="#555f62" stroke-width="10" stroke-linecap="round" />

    {{-- Module --}}
    <path d="M30 70 390 22 388 332 34 392Z" fill="url(#{{ $uid }}-glass)" stroke="#d9dfe0" stroke-width="9" stroke-linejoin="round" />

    <g stroke="#9fb0c9" stroke-opacity="0.55" stroke-width="1.6">
        @foreach ($vertical as [$a, $b])
            <path d="M{{ round($a[0], 1) }} {{ round($a[1], 1) }}L{{ round($b[0], 1) }} {{ round($b[1], 1) }}" />
        @endforeach
        @foreach ($horizontal as [$a, $b])
            <path d="M{{ round($a[0], 1) }} {{ round($a[1], 1) }}L{{ round($b[0], 1) }} {{ round($b[1], 1) }}" />
        @endforeach
    </g>

    {{-- Fine busbars inside each cell column --}}
    <g stroke="#7f93b3" stroke-opacity="0.25" stroke-width="0.9">
        @for ($i = 0; $i < $cols * 3; $i++)
            @php
                $t = ($i + 0.5) / ($cols * 3);
                $a = $lerp($tl, $tr, $t);
                $b = $lerp($bl, $br, $t);
            @endphp
            <path d="M{{ round($a[0], 1) }} {{ round($a[1], 1) }}L{{ round($b[0], 1) }} {{ round($b[1], 1) }}" />
        @endfor
    </g>

    <path d="M30 70 390 22 388 332 34 392Z" fill="url(#{{ $uid }}-shine)" />
</svg>
