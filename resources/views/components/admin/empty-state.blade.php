@props([
    'title' => 'Belum ada data',
    'description' => null,
    'icon' => 'inbox',
])

<flux:card class="flex flex-col items-center gap-3 py-14 text-center">
    <span class="grid size-14 place-items-center rounded-2xl bg-zinc-100 text-zinc-400 dark:bg-zinc-700/40 dark:text-zinc-500">
        <flux:icon :name="$icon" class="size-7" />
    </span>

    <div class="space-y-1">
        <flux:heading>{{ $title }}</flux:heading>

        @if (filled($description))
            <flux:text class="mx-auto max-w-md">{{ $description }}</flux:text>
        @endif
    </div>

    @if (! $slot->isEmpty())
        <div class="mt-1">{{ $slot }}</div>
    @endif
</flux:card>
