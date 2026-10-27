<?php

use App\Models\Subscriber;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

new #[Title('Pelanggan Email')] class extends Component {
    use WithPagination;

    #[Url(as: 'cari')]
    public string $search = '';

    #[Url(as: 'status')]
    public string $status = '';

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

    /**
     * Base query honouring the current search and status filters.
     */
    protected function filtered()
    {
        return Subscriber::query()
            ->when($this->status === 'active', fn ($query) => $query->where('is_active', true))
            ->when($this->status === 'inactive', fn ($query) => $query->where('is_active', false))
            ->when(filled($this->search), function ($query) {
                $like = '%'.str_replace(['%', '_'], ['\%', '\_'], $this->search).'%';

                $query->where(function ($inner) use ($like) {
                    $inner->where('email', 'like', $like)
                        ->orWhere('source', 'like', $like);
                });
            });
    }

    #[Computed]
    public function subscribers()
    {
        return $this->filtered()
            ->ordered()
            ->paginate(20)
            ->withQueryString();
    }

    #[Computed]
    public function counts(): array
    {
        return [
            'all' => Subscriber::query()->count(),
            'active' => Subscriber::query()->where('is_active', true)->count(),
            'inactive' => Subscriber::query()->where('is_active', false)->count(),
            'month' => Subscriber::query()->where('created_at', '>=', now()->startOfMonth())->count(),
        ];
    }

    public function toggle(int $subscriberId): void
    {
        $subscriber = Subscriber::query()->findOrFail($subscriberId);

        $subscriber->forceFill(['is_active' => ! $subscriber->is_active])->save();

        Flux::toast(
            variant: 'success',
            text: $subscriber->is_active
                ? $subscriber->email.' diaktifkan kembali.'
                : $subscriber->email.' dinonaktifkan.',
        );
    }

    public function confirmDelete(int $subscriberId): void
    {
        $this->confirmingId = $subscriberId;

        $this->dispatch('modal-show', name: 'hapus-pelanggan');
    }

    public function destroy(): void
    {
        $subscriber = Subscriber::query()->findOrFail($this->confirmingId);

        $subscriber->delete();

        Flux::toast(variant: 'success', text: $subscriber->email.' dihapus dari daftar.');

        $this->confirmingId = null;
        $this->dispatch('modal-close', name: 'hapus-pelanggan');
    }

    #[Computed]
    public function confirmingEmail(): string
    {
        return $this->confirmingId
            ? (string) (Subscriber::query()->whereKey($this->confirmingId)->value('email') ?? '')
            : '';
    }

    /**
     * Export the filtered list as CSV. Returning the response makes Livewire
     * hand it to the browser as a download.
     */
    public function export(): StreamedResponse
    {
        $filename = 'pelanggan-email-'.now()->format('Y-m-d-His').'.csv';

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'wb');

            fputcsv($handle, ['Email', 'Sumber', 'Status', 'Berlangganan sejak', 'Terdaftar']);

            $this->filtered()
                ->ordered()
                ->each(function (Subscriber $subscriber) use ($handle) {
                    fputcsv($handle, [
                        $subscriber->email,
                        $subscriber->source ?: '-',
                        $subscriber->is_active ? 'aktif' : 'nonaktif',
                        $subscriber->subscribed_at?->format('Y-m-d H:i') ?: '-',
                        $subscriber->created_at?->format('Y-m-d H:i') ?: '-',
                    ]);
                });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}; ?>

<div class="space-y-6">
    <x-admin.page-header
        title="Pelanggan Email"
        description="Daftar alamat yang berlangganan warta dari formulir situs. Gunakan ekspor CSV untuk mengirim kampanye."
    >
        <flux:button
            icon="arrow-down-tray"
            type="button"
            variant="filled"
            wire:click="export"
            :disabled="$this->counts['all'] === 0"
        >
            Ekspor CSV
        </flux:button>
    </x-admin.page-header>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-admin.stat-card label="Total pelanggan" :value="$this->counts['all']" icon="users" hint="semua alamat" />
        <x-admin.stat-card label="Aktif" :value="$this->counts['active']" icon="check-circle" hint="siap dikirimi" />
        <x-admin.stat-card label="Nonaktif" :value="$this->counts['inactive']" icon="x-circle" hint="berhenti berlangganan" />
        <x-admin.stat-card label="Bulan ini" :value="$this->counts['month']" icon="calendar" hint="pendaftar baru" />
    </div>

    <flux:card class="space-y-4">
        <div class="grid gap-4 md:grid-cols-3">
            <flux:input
                wire:model.live.debounce.400ms="search"
                label="Cari"
                icon="magnifying-glass"
                placeholder="Email atau sumber…"
                type="search"
                class="md:col-span-2"
            />

            <flux:select wire:model.live="status" label="Status" placeholder="Semua status">
                <flux:select.option value="active">Aktif</flux:select.option>
                <flux:select.option value="inactive">Nonaktif</flux:select.option>
            </flux:select>
        </div>

        @if ($search !== '' || $status !== '')
            <flux:button size="sm" variant="ghost" icon="x-mark" type="button" wire:click="resetFilters">
                Bersihkan filter
            </flux:button>
        @endif
    </flux:card>

    @if ($this->subscribers->isEmpty())
        <x-admin.empty-state
            title="Tidak ada pelanggan"
            description="Belum ada alamat email yang cocok dengan filter Anda."
            icon="users"
        />
    @else
        <flux:card class="overflow-hidden">
            <div class="-mx-4 overflow-x-auto sm:mx-0">
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>Email</flux:table.column>
                        <flux:table.column class="hidden sm:table-cell">Sumber</flux:table.column>
                        <flux:table.column align="center">Status</flux:table.column>
                        <flux:table.column class="hidden lg:table-cell">Berlangganan</flux:table.column>
                        <flux:table.column align="end">Aksi</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @foreach ($this->subscribers as $subscriber)
                            <flux:table.row wire:key="pelanggan-{{ $subscriber->id }}">
                                <flux:table.cell>
                                    <p class="max-w-64 truncate font-medium text-zinc-800 dark:text-zinc-100">
                                        {{ $subscriber->email }}
                                    </p>
                                </flux:table.cell>

                                <flux:table.cell class="hidden sm:table-cell">
                                    <span class="max-w-40 truncate">{{ $subscriber->source ?: '—' }}</span>
                                </flux:table.cell>

                                <flux:table.cell align="center">
                                    <flux:badge :color="$subscriber->is_active ? 'green' : null" inset="left right">
                                        {{ $subscriber->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </flux:badge>
                                </flux:table.cell>

                                <flux:table.cell class="hidden lg:table-cell">
                                    @if ($subscriber->subscribed_at)
                                        <flux:tooltip :content="$subscriber->subscribed_at->translatedFormat('j F Y, H.i')">
                                            <span>{{ $subscriber->subscribed_at->diffForHumans() }}</span>
                                        </flux:tooltip>
                                    @else
                                        <span>—</span>
                                    @endif
                                </flux:table.cell>

                                <flux:table.cell align="end">
                                    <div class="flex justify-end gap-1">
                                        <flux:button
                                            size="xs"
                                            variant="ghost"
                                            :icon="$subscriber->is_active ? 'x-circle' : 'check-circle'"
                                            type="button"
                                            wire:click="toggle({{ $subscriber->id }})"
                                        >
                                            <span class="sr-only">
                                                {{ $subscriber->is_active ? 'Nonaktifkan' : 'Aktifkan' }} {{ $subscriber->email }}
                                            </span>
                                        </flux:button>

                                        <flux:button
                                            size="xs"
                                            variant="ghost"
                                            icon="trash"
                                            type="button"
                                            class="text-red-500"
                                            wire:click="confirmDelete({{ $subscriber->id }})"
                                        >
                                            <span class="sr-only">Hapus {{ $subscriber->email }}</span>
                                        </flux:button>
                                    </div>
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            </div>

            <flux:pagination :paginator="$this->subscribers" class="mt-4" />
        </flux:card>
    @endif

    <x-admin.confirm-delete name="hapus-pelanggan" title="Hapus pelanggan?">
        Alamat “{{ $this->confirmingEmail }}” akan dihapus permanen. Orang tersebut tetap bisa berlangganan lagi nanti.
    </x-admin.confirm-delete>
</div>
