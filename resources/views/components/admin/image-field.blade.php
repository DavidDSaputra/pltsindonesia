@props([
    'label' => 'Gambar',
    'model',
    'pathModel',
    'preview' => null,
    'description' => null,
])

@php
    $fileId = 'berkas-'.$pathModel;
@endphp

<div class="space-y-3">
    <div>
        <flux:label :for="$fileId">{{ $label }}</flux:label>

        <div class="mt-2 flex flex-wrap items-start gap-4">
            <div
                class="grid size-28 shrink-0 place-items-center overflow-hidden rounded-xl border border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900"
                @if ($preview) wire:loading.class="opacity-50" wire:target="{{ $model }}" @endif
            >
                @if ($preview)
                    <img src="{{ $preview }}" alt="" class="size-full object-cover">
                @else
                    <flux:icon name="photo" class="size-6 text-zinc-400" />
                @endif
            </div>

            <div class="min-w-52 flex-1 space-y-2">
                <input
                    id="{{ $fileId }}"
                    type="file"
                    accept="image/*"
                    wire:model="{{ $model }}"
                    class="block w-full cursor-pointer text-sm text-zinc-500 file:me-3 file:cursor-pointer file:rounded-lg file:border-0 file:bg-zinc-900 file:px-3 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-zinc-700 dark:text-zinc-400 dark:file:bg-zinc-50 dark:file:text-zinc-900 dark:hover:file:bg-zinc-200"
                >

                <div wire:loading.remove wire:target="{{ $model }}">
                    <flux:text size="sm">JPG, PNG, WebP, atau SVG. Maksimal 4 MB.</flux:text>
                </div>

                <div wire:loading wire:target="{{ $model }}">
                    <flux:text size="sm" color="amber">Mengunggah…</flux:text>
                </div>
            </div>
        </div>

        <flux:error :name="$model" />
    </div>

    <flux:input
        wire:model.blur="{{ $pathModel }}"
        label="Atau tempel URL / path gambar"
        :description="$description"
        placeholder="images/contoh.jpg atau https://…"
        type="text"
    />
</div>
