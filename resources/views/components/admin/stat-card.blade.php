@props([
    'label',
    'value',
    'icon' => 'chart-bar',
    'hint' => null,
])

<flux:card class="flex items-start gap-4">
    <span class="grid size-11 shrink-0 place-items-center rounded-xl bg-zinc-100 text-zinc-600 dark:bg-zinc-700/50 dark:text-zinc-300">
        <flux:icon :name="$icon" class="size-5" />
    </span>

    <div class="min-w-0 space-y-0.5">
        <flux:text size="sm" class="truncate">{{ $label }}</flux:text>

        <p class="text-2xl font-bold leading-none tracking-tight text-zinc-900 dark:text-zinc-50">
            {{ $value }}
        </p>

        @if (filled($hint))
            <flux:text size="sm" class="truncate">{{ $hint }}</flux:text>
        @endif
    </div>
</flux:card>
