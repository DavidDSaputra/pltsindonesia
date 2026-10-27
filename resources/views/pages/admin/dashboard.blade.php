<?php

use App\Enums\ArticleStatus;
use App\Enums\MessageStatus;
use App\Models\Article;
use App\Models\ContactMessage;
use App\Models\Gallery;
use App\Models\Media;
use App\Models\Project;
use App\Models\Service;
use App\Models\Subscriber;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Dasbor CMS')] class extends Component {
    /**
     * Headline numbers for the CMS overview.
     *
     * @return array<int, array{label: string, value: string|int, icon: string, hint: string}>
     */
    #[Computed]
    public function stats(): array
    {
        $published = Article::query()->published()->count();
        $drafts = Article::query()->where('status', ArticleStatus::Draft)->count();
        $unread = ContactMessage::query()->unread()->count();

        return [
            [
                'label' => 'Artikel terbit',
                'value' => $published,
                'icon' => 'newspaper',
                'hint' => $drafts.' draf menunggu',
            ],
            [
                'label' => 'Pesan baru',
                'value' => $unread,
                'icon' => 'inbox',
                'hint' => ContactMessage::query()->count().' total pesan',
            ],
            [
                'label' => 'Pelanggan email',
                'value' => Subscriber::query()->active()->count(),
                'icon' => 'envelope',
                'hint' => 'langganan aktif',
            ],
            [
                'label' => 'Layanan',
                'value' => Service::query()->count(),
                'icon' => 'wrench-screwdriver',
                'hint' => Service::query()->active()->count().' tayang',
            ],
            [
                'label' => 'Proyek',
                'value' => Project::query()->count(),
                'icon' => 'briefcase',
                'hint' => Project::query()->active()->count().' tayang',
            ],
            [
                'label' => 'Berkas media',
                'value' => Media::query()->count(),
                'icon' => 'photo',
                'hint' => Gallery::query()->count().' foto galeri',
            ],
        ];
    }

    #[Computed]
    public function recentMessages()
    {
        return ContactMessage::query()->ordered()->limit(5)->get();
    }

    #[Computed]
    public function recentArticles()
    {
        return Article::query()
            ->with('category')
            ->orderByDesc('updated_at')
            ->limit(5)
            ->get();
    }

    /**
     * Mark a message as read straight from the dashboard.
     */
    public function markRead(int $messageId): void
    {
        $message = ContactMessage::query()->findOrFail($messageId);

        $message->markAs(MessageStatus::Read);

        Flux::toast(variant: 'success', text: 'Pesan ditandai sudah dibaca.');
    }
}; ?>

<div class="space-y-8">
    <x-admin.page-header
        title="Dasbor"
        :description="'Ringkasan konten dan prospek '.site('company_name', config('app.name')).'.'"
    >
        <flux:button :href="route('home')" target="_blank" icon="arrow-top-right-on-square">
            Lihat Situs
        </flux:button>
    </x-admin.page-header>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @foreach ($this->stats as $stat)
            <x-admin.stat-card
                :label="$stat['label']"
                :value="$stat['value']"
                :icon="$stat['icon']"
                :hint="$stat['hint']"
            />
        @endforeach
    </div>

    <div class="grid gap-6 xl:grid-cols-2">
        <flux:card>
            <div class="mb-4 flex items-center justify-between gap-3">
                <flux:heading level="2">Pesan terbaru</flux:heading>

                <flux:link :href="route('admin.messages.index')" wire:navigate>Lihat semua</flux:link>
            </div>

            @if ($this->recentMessages->isEmpty())
                <flux:text>Belum ada pesan masuk dari formulir kontak.</flux:text>
            @else
                <ul class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @foreach ($this->recentMessages as $message)
                        <li class="flex items-start gap-3 py-3 first:pt-0 last:pb-0">
                            <flux:badge :color="$message->status->color()" size="sm" inset="top bottom">
                                {{ $message->status->label() }}
                            </flux:badge>

                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium text-zinc-800 dark:text-zinc-100">
                                    {{ $message->subject ?: '(tanpa subjek)' }}
                                </p>

                                <flux:text size="sm" class="truncate">
                                    {{ $message->name }} · {{ $message->created_at->diffForHumans() }}
                                </flux:text>
                            </div>

                            @if ($message->status === MessageStatus::New)
                                <flux:button
                                    size="xs"
                                    variant="ghost"
                                    icon="check"
                                    wire:click="markRead({{ $message->id }})"
                                    wire:loading.attr="disabled"
                                >
                                    <span class="sr-only">Tandai sudah dibaca</span>
                                    Baca
                                </flux:button>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        </flux:card>

        <flux:card>
            <div class="mb-4 flex items-center justify-between gap-3">
                <flux:heading level="2">Artikel terakhir diubah</flux:heading>

                <flux:link :href="route('admin.articles.index')" wire:navigate>Lihat semua</flux:link>
            </div>

            @if ($this->recentArticles->isEmpty())
                <flux:text>Belum ada artikel. Mulai tulis artikel pertama Anda.</flux:text>
            @else
                <ul class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @foreach ($this->recentArticles as $article)
                        <li class="flex items-center gap-3 py-3 first:pt-0 last:pb-0">
                            <div class="min-w-0 flex-1">
                                <flux:link
                                    class="block truncate text-sm font-medium"
                                    :href="route('admin.articles.edit', $article)"
                                    wire:navigate
                                >
                                    {{ $article->title }}
                                </flux:link>

                                <flux:text size="sm" class="truncate">
                                    {{ $article->category?->name ?? 'Tanpa kategori' }}
                                    · {{ $article->views_count }} tayang
                                </flux:text>
                            </div>

                            <flux:badge :color="$article->status->color()" size="sm">
                                {{ $article->status->label() }}
                            </flux:badge>
                        </li>
                    @endforeach
                </ul>
            @endif
        </flux:card>
    </div>

    <flux:card>
        <flux:heading level="2" class="mb-4">Aksi cepat</flux:heading>

        <div class="flex flex-wrap gap-2">
            <flux:button :href="route('admin.articles.create')" variant="primary" icon="plus" wire:navigate>
                Tulis artikel
            </flux:button>

            <flux:button :href="route('admin.services.index')" icon="wrench-screwdriver" wire:navigate>
                Kelola layanan
            </flux:button>

            <flux:button :href="route('admin.projects.index')" icon="briefcase" wire:navigate>
                Kelola proyek
            </flux:button>

            <flux:button :href="route('admin.faqs.index')" icon="question-mark-circle" wire:navigate>
                Kelola FAQ
            </flux:button>

            <flux:button :href="route('admin.galleries.index')" icon="rectangle-group" wire:navigate>
                Kelola galeri
            </flux:button>

            <flux:button :href="route('admin.testimonials.index')" icon="star" wire:navigate>
                Kelola testimoni
            </flux:button>

            <flux:button :href="route('admin.media.index')" icon="photo" wire:navigate>
                Pustaka media
            </flux:button>

            <flux:button :href="route('admin.settings.index')" icon="cog" wire:navigate>
                Pengaturan situs
            </flux:button>
        </div>
    </flux:card>
</div>
