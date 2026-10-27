<?php

use App\Enums\MessageStatus;
use App\Models\ContactMessage;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Pesan Masuk')] class extends Component {
    use WithPagination;

    #[Url(as: 'cari')]
    public string $search = '';

    #[Url(as: 'status')]
    public string $status = '';

    public ?int $openId = null;

    public ?int $confirmingId = null;

    public function updating($property): void
    {
        if (in_array($property, ['search', 'status'], true)) {
            $this->resetPage();
        }
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'status']);
        $this->resetPage();
    }

    #[Computed]
    public function messages()
    {
        return ContactMessage::query()
            ->when($this->status !== '', fn ($query) => $query->where('status', $this->status))
            ->when(filled($this->search), function ($query) {
                $like = '%'.str_replace(['%', '_'], ['\%', '\_'], $this->search).'%';

                $query->where(function ($inner) use ($like) {
                    $inner->where('name', 'like', $like)
                        ->orWhere('email', 'like', $like)
                        ->orWhere('subject', 'like', $like)
                        ->orWhere('message', 'like', $like);
                });
            })
            ->ordered()
            ->paginate(15)
            ->withQueryString();
    }

    #[Computed]
    public function counts(): array
    {
        return [
            'all' => ContactMessage::query()->count(),
            'new' => ContactMessage::query()->where('status', MessageStatus::New)->count(),
            'read' => ContactMessage::query()->where('status', MessageStatus::Read)->count(),
            'replied' => ContactMessage::query()->where('status', MessageStatus::Replied)->count(),
            'archived' => ContactMessage::query()->where('status', MessageStatus::Archived)->count(),
        ];
    }

    /**
     * Open a message and immediately mark it as read.
     */
    public function open(int $messageId): void
    {
        $message = ContactMessage::query()->findOrFail($messageId);

        if ($message->status === MessageStatus::New) {
            $message->markAs(MessageStatus::Read);
        }

        $this->openId = $message->id;

        $this->dispatch('modal-show', name: 'pesan');
    }

    public function close(): void
    {
        $this->openId = null;

        $this->dispatch('modal-close', name: 'pesan');
    }

    #[Computed]
    public function message(): ?ContactMessage
    {
        return $this->openId ? ContactMessage::query()->find($this->openId) : null;
    }

    public function markAs(string $status): void
    {
        abort_unless($this->openId !== null, 404);

        $message = ContactMessage::query()->findOrFail($this->openId);

        $message->markAs(MessageStatus::from($status));

        Flux::toast(variant: 'success', text: 'Status pesan diubah menjadi '.$message->status->label().'.');
    }

    public function confirmDelete(int $messageId): void
    {
        $this->confirmingId = $messageId;

        $this->dispatch('modal-show', name: 'hapus-pesan');
    }

    public function destroy(): void
    {
        $message = ContactMessage::query()->findOrFail($this->confirmingId);

        if ($this->openId === $message->id) {
            $this->close();
        }

        $message->delete();

        Flux::toast(variant: 'success', text: 'Pesan dihapus.');

        $this->confirmingId = null;
        $this->dispatch('modal-close', name: 'hapus-pesan');
    }

    #[Computed]
    public function confirmingSubject(): string
    {
        return $this->confirmingId
            ? (string) (ContactMessage::query()->whereKey($this->confirmingId)->value('subject') ?? '(tanpa subjek)')
            : '';
    }
}; ?>

<div class="space-y-6">
    <x-admin.page-header
        title="Pesan Masuk"
        description="Semua kiriman dari formulir kontak situs. Pesan baru otomatis ditandai sudah dibaca saat dibuka."
    />

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
        <x-admin.stat-card label="Total pesan" :value="$this->counts['all']" icon="inbox" hint="sejak awal" />
        <x-admin.stat-card label="Baru" :value="$this->counts['new']" icon="sparkles" hint="belum dibaca" />
        <x-admin.stat-card label="Dibaca" :value="$this->counts['read']" icon="eye" hint="menunggu balasan" />
        <x-admin.stat-card label="Dibalas" :value="$this->counts['replied']" icon="paper-airplane" hint="sudah ditindaklanjuti" />
        <x-admin.stat-card label="Arsip" :value="$this->counts['archived']" icon="archive-box" hint="disimpan saja" />
    </div>

    <flux:card class="space-y-4">
        <div class="grid gap-4 md:grid-cols-3">
            <flux:input
                wire:model.live.debounce.400ms="search"
                label="Cari"
                icon="magnifying-glass"
                placeholder="Nama, email, subjek, atau isi pesan…"
                type="search"
                class="md:col-span-2"
            />

            <flux:select wire:model.live="status" label="Status" placeholder="Semua status">
                @foreach (MessageStatus::cases() as $option)
                    <flux:select.option value="{{ $option->value }}">{{ $option->label() }}</flux:select.option>
                @endforeach
            </flux:select>
        </div>

        @if ($search !== '' || $status !== '')
            <flux:button size="sm" variant="ghost" icon="x-mark" type="button" wire:click="resetFilters">
                Bersihkan filter
            </flux:button>
        @endif
    </flux:card>

    @if ($this->messages->isEmpty())
        <x-admin.empty-state
            title="Tidak ada pesan"
            description="Belum ada kiriman yang cocok dengan pencarian Anda."
            icon="inbox"
        />
    @else
        <flux:card class="overflow-hidden">
            <div class="-mx-4 overflow-x-auto sm:mx-0">
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>Pengirim</flux:table.column>
                        <flux:table.column class="hidden md:table-cell">Subjek</flux:table.column>
                        <flux:table.column align="center">Status</flux:table.column>
                        <flux:table.column class="hidden lg:table-cell">Diterima</flux:table.column>
                        <flux:table.column align="end">Aksi</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @foreach ($this->messages as $message)
                            <flux:table.row wire:key="pesan-{{ $message->id }}">
                                <flux:table.cell>
                                    <div class="min-w-0">
                                        <p class="max-w-52 truncate font-medium text-zinc-800 dark:text-zinc-100">
                                            {{ $message->name }}
                                        </p>

                                        <flux:text size="sm" class="max-w-52 truncate">{{ $message->email }}</flux:text>
                                    </div>
                                </flux:table.cell>

                                <flux:table.cell class="hidden md:table-cell">
                                    <p class="max-w-72 truncate">
                                        {{ $message->subject ?: '(tanpa subjek)' }}
                                    </p>

                                    <flux:text size="sm" class="max-w-72 truncate">{{ $message->message }}</flux:text>
                                </flux:table.cell>

                                <flux:table.cell align="center">
                                    <flux:badge :color="$message->status->color()" inset="left right">
                                        {{ $message->status->label() }}
                                    </flux:badge>
                                </flux:table.cell>

                                <flux:table.cell class="hidden lg:table-cell">
                                    <flux:tooltip :content="$message->created_at->translatedFormat('j F Y, H.i')">
                                        <span>{{ $message->created_at->diffForHumans() }}</span>
                                    </flux:tooltip>
                                </flux:table.cell>

                                <flux:table.cell align="end">
                                    <div class="flex justify-end gap-1">
                                        <flux:button size="xs" variant="ghost" icon="eye" type="button" wire:click="open({{ $message->id }})">
                                            <span class="sr-only">Buka pesan dari {{ $message->name }}</span>
                                        </flux:button>

                                        <flux:button
                                            size="xs"
                                            variant="ghost"
                                            icon="trash"
                                            type="button"
                                            class="text-red-500"
                                            wire:click="confirmDelete({{ $message->id }})"
                                        >
                                            <span class="sr-only">Hapus pesan dari {{ $message->name }}</span>
                                        </flux:button>
                                    </div>
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            </div>

            <flux:pagination :paginator="$this->messages" class="mt-4" />
        </flux:card>
    @endif

    <flux:modal name="pesan" scroll="body" class="min-w-[24rem] max-w-2xl space-y-5">
        @if ($message = $this->message)
            <div class="space-y-3">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div class="min-w-0">
                        <flux:heading level="2" size="lg">{{ $message->subject ?: '(tanpa subjek)' }}</flux:heading>

                        <flux:text size="sm">
                            Diterima {{ $message->created_at->translatedFormat('j F Y, H.i') }} ·
                            {{ $message->created_at->diffForHumans() }}
                        </flux:text>
                    </div>

                    <flux:badge :color="$message->status->color()" inset="left right">
                        {{ $message->status->label() }}
                    </flux:badge>
                </div>

                <dl class="grid gap-3 rounded-xl bg-zinc-50 p-4 text-sm sm:grid-cols-2 dark:bg-zinc-900/40">
                    <div>
                        <dt class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Nama</dt>
                        <dd class="text-zinc-800 dark:text-zinc-100">{{ $message->name }}</dd>
                    </div>

                    <div>
                        <dt class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Email</dt>
                        <dd>
                            <flux:link :href="'mailto:'.$message->email">{{ $message->email }}</flux:link>
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Telepon</dt>
                        <dd class="text-zinc-800 dark:text-zinc-100">{{ $message->phone ?: '—' }}</dd>
                    </div>

                    <div>
                        <dt class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Alamat IP</dt>
                        <dd class="text-zinc-800 dark:text-zinc-100">{{ $message->ip_address ?: '—' }}</dd>
                    </div>
                </dl>

                <div class="space-y-1.5">
                    <flux:heading level="3" size="base">Isi pesan</flux:heading>

                    <p class="whitespace-pre-line text-sm leading-relaxed text-zinc-700 dark:text-zinc-300">{{ $message->message }}</p>
                </div>

                <div class="flex flex-wrap gap-2 border-t border-zinc-200 pt-4 dark:border-zinc-700">
                    <flux:button
                        variant="primary"
                        icon="paper-airplane"
                        :href="'mailto:'.rawurlencode($message->email).'?subject='.rawurlencode('Re: '.($message->subject ?: 'Kontak situs'))"
                    >
                        Balas lewat Email
                    </flux:button>

                    @if ($message->status !== MessageStatus::Replied)
                        <flux:button icon="check-circle" type="button" wire:click="markAs('replied')">
                            Tandai Sudah Dibalas
                        </flux:button>
                    @endif

                    <flux:button icon="archive-box" type="button" wire:click="markAs('archived')">
                        Arsipkan
                    </flux:button>

                    <flux:spacer />

                    <flux:modal.close>
                        <flux:button variant="ghost" type="button" wire:click="close">Tutup</flux:button>
                    </flux:modal.close>
                </div>
            </div>
        @endif
    </flux:modal>

    <x-admin.confirm-delete name="hapus-pesan" title="Hapus pesan?">
        Pesan “{{ $this->confirmingSubject }}” akan dihapus permanen dari daftar.
    </x-admin.confirm-delete>
</div>
