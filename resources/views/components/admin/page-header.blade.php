@props([
    'title',
    'description' => null,
    'href' => null,
    'label' => null,
])

<div class="mb-6 flex flex-wrap items-end justify-between gap-4">
    <div class="space-y-1">
        <flux:heading size="xl" level="1">{{ $title }}</flux:heading>

        @if (filled($description))
            <flux:text class="max-w-2xl">{{ $description }}</flux:text>
        @endif
    </div>

    @if ($href !== null || ! $slot->isEmpty())
        <div class="flex shrink-0 items-center gap-2">
            {{ $slot }}

            @if ($href !== null)
                <flux:button :href="$href" variant="primary" icon="plus" wire:navigate>
                    {{ $label ?? 'Tambah' }}
                </flux:button>
            @endif
        </div>
    @endif
</div>
