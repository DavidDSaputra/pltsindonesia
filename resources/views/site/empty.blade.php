@php
    $pageTitle = $title ?? config('app.name');
    $crumbs = [
        ['label' => 'Beranda', 'url' => route('home')],
        ['label' => $pageTitle],
    ];
@endphp

<x-site.layout :title="$pageTitle">
    <x-site.page-hero
        :crumbs="$crumbs"
        eyebrow="Informasi"
        :title="$pageTitle"
        description="Konten halaman ini sedang dipersiapkan dan akan segera diperbarui."
    />

    <div class="min-h-[28vh] bg-paper"></div>
</x-site.layout>
