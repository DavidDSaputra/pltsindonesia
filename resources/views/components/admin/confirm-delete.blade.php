@props([
    'name',
    'title' => 'Hapus data ini?',
    'action' => 'destroy',
    'confirmLabel' => 'Hapus',
])

<flux:modal :name="$name" class="min-w-[22rem] space-y-6">
    <div class="space-y-2">
        <flux:heading level="2" size="lg">{{ $title }}</flux:heading>

        <flux:text>{{ $slot }}</flux:text>
    </div>

    <div class="flex gap-2">
        <flux:spacer />

        <flux:modal.close>
            <flux:button variant="ghost" type="button">Batal</flux:button>
        </flux:modal.close>

        <flux:button variant="danger" type="button" icon="trash" wire:click="{{ $action }}">
            {{ $confirmLabel }}
        </flux:button>
    </div>
</flux:modal>
