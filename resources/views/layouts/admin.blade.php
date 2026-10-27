<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')

        <meta name="robots" content="noindex, nofollow">
    </head>

    <body class="min-h-screen bg-white dark:bg-zinc-800">
        @php
            $nav = [
                ['heading' => 'Konten', 'items' => [
                    ['label' => 'Dasbor', 'icon' => 'sun', 'route' => 'admin.dashboard'],
                    ['label' => 'Artikel', 'icon' => 'newspaper', 'route' => 'admin.articles.index'],
                    ['label' => 'Kategori', 'icon' => 'folder', 'route' => 'admin.categories.index'],
                    ['label' => 'Tag', 'icon' => 'tag', 'route' => 'admin.tags.index'],
                    ['label' => 'Media', 'icon' => 'photo', 'route' => 'admin.media.index'],
                ]],
                ['heading' => 'Halaman Situs', 'items' => [
                    ['label' => 'Layanan', 'icon' => 'wrench-screwdriver', 'route' => 'admin.services.index'],
                    ['label' => 'Proyek', 'icon' => 'briefcase', 'route' => 'admin.projects.index'],
                    ['label' => 'Testimoni', 'icon' => 'star', 'route' => 'admin.testimonials.index'],
                    ['label' => 'FAQ', 'icon' => 'question-mark-circle', 'route' => 'admin.faqs.index'],
                    ['label' => 'Galeri', 'icon' => 'rectangle-group', 'route' => 'admin.galleries.index'],
                ]],
                ['heading' => 'Prospek', 'items' => [
                    ['label' => 'Pesan Masuk', 'icon' => 'inbox', 'route' => 'admin.messages.index'],
                    ['label' => 'Pelanggan Email', 'icon' => 'envelope', 'route' => 'admin.subscribers.index'],
                ]],
                ['heading' => 'Sistem', 'items' => [
                    ['label' => 'Pengaturan', 'icon' => 'cog', 'route' => 'admin.settings.index'],
                    ...(auth()->user()->isAdmin()
                        ? [['label' => 'Pengguna', 'icon' => 'users', 'route' => 'admin.users.index']]
                        : []),
                ]],
            ];
        @endphp

        <flux:sidebar sticky collapsible="mobile" class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.header>
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5" wire:navigate>
                    <span class="grid size-8 place-items-center rounded-lg bg-solar-400 text-zinc-900" aria-hidden="true">
                        <svg viewBox="0 0 24 24" class="size-5" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="8" r="3.2" />
                            <path d="M12 2v1.6M6.6 3.6 7.7 4.7M17.4 3.6 16.3 4.7" />
                            <path d="M3.5 14h17l1.5 6.5H2L3.5 14Z" />
                            <path d="M8 14 7 20.5M16 14l1 6.5M4.6 17.2h14.8" />
                        </svg>
                    </span>

                    <span class="leading-tight">
                        <span class="block text-sm font-bold">CMS</span>
                        <span class="block text-xs text-zinc-500 dark:text-zinc-400">{{ site('company_name', config('app.name')) }}</span>
                    </span>
                </a>

                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                @foreach ($nav as $group)
                    <flux:sidebar.group :heading="$group['heading']" class="grid">
                        @foreach ($group['items'] as $item)
                            <flux:sidebar.item
                                :icon="$item['icon']"
                                :href="route($item['route'])"
                                :current="request()->routeIs($item['route']) || request()->routeIs(str_replace('.index', '.*', $item['route']))"
                                wire:navigate
                            >
                                {{ $item['label'] }}

                                @if ($item['route'] === 'admin.messages.index' && ($unread = \App\Models\ContactMessage::query()->unread()->count()) > 0)
                                    <flux:badge size="sm" color="amber" inset="left right">{{ $unread }}</flux:badge>
                                @endif
                            </flux:sidebar.item>
                        @endforeach
                    </flux:sidebar.group>
                @endforeach
            </flux:sidebar.nav>

            <flux:spacer />

            <flux:sidebar.nav>
                <flux:sidebar.item icon="arrow-top-right-on-square" :href="route('home')" target="_blank">
                    Lihat Situs
                </flux:sidebar.item>

                <flux:sidebar.item icon="cog-6-tooth" :href="route('profile.edit')" wire:navigate>
                    Profil Saya
                </flux:sidebar.item>
            </flux:sidebar.nav>

            <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
        </flux:sidebar>

        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile :initials="auth()->user()->initials()" icon-trailing="chevron-down" />

                <flux:menu>
                    <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                        <flux:avatar :name="auth()->user()->name" :initials="auth()->user()->initials()" />

                        <div class="grid flex-1 text-start text-sm leading-tight">
                            <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                            <flux:text class="truncate">{{ auth()->user()->role->label() }}</flux:text>
                        </div>
                    </div>

                    <flux:menu.separator />

                    <flux:menu.item :href="route('home')" icon="arrow-top-right-on-square" target="_blank">
                        Lihat Situs
                    </flux:menu.item>

                    <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                        Profil Saya
                    </flux:menu.item>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full cursor-pointer">
                            Keluar
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
