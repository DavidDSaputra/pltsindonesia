<?php

use App\Models\Faq;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

new #[Title('FAQ')] class extends Component {
    #[Url(as: 'grup')]
    public string $groupFilter = '';

    public ?int $faqId = null;

    public string $question = '';

    public string $answer = '';

    public string $group = '';

    public bool $is_active = true;

    public string $sort_order = '0';

    public ?int $confirmingId = null;

    /**
     * @return array<string, mixed>
     */
    protected function rulesFor(): array
    {
        return [
            'question' => ['required', 'string', 'max:220'],
            'answer' => ['required', 'string', 'max:4000'],
            'group' => ['nullable', 'string', 'max:80'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function validationAttributes(): array
    {
        return [
            'question' => 'pertanyaan',
            'answer' => 'jawaban',
            'group' => 'grup',
            'sort_order' => 'urutan',
        ];
    }

    #[Computed]
    public function faqs()
    {
        return Faq::query()
            ->when($this->groupFilter !== '', fn ($query) => $query->where('group', $this->groupFilter))
            ->ordered()
            ->get();
    }

    /**
     * Every group label in use, including inactive rows.
     *
     * @return array<int, string>
     */
    #[Computed]
    public function groups(): array
    {
        return Faq::query()
            ->whereNotNull('group')
            ->where('group', '!=', '')
            ->distinct()
            ->orderBy('group')
            ->pluck('group')
            ->all();
    }

    public function create(): void
    {
        $this->resetForm();
        $this->sort_order = (string) ((int) Faq::query()->max('sort_order') + 10);
        $this->group = $this->groupFilter;

        $this->dispatch('modal-show', name: 'faq');
    }

    public function edit(int $faqId): void
    {
        $faq = Faq::query()->findOrFail($faqId);

        $this->faqId = $faq->id;
        $this->question = (string) $faq->question;
        $this->answer = (string) $faq->answer;
        $this->group = (string) $faq->group;
        $this->is_active = (bool) $faq->is_active;
        $this->sort_order = (string) $faq->sort_order;
        $this->resetValidation();

        $this->dispatch('modal-show', name: 'faq');
    }

    public function save(): void
    {
        $data = $this->validate($this->rulesFor(), attributes: $this->validationAttributes());

        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['group'] = filled($data['group']) ? $data['group'] : null;
        $data['is_active'] = $this->is_active;

        $faq = $this->faqId ? Faq::query()->findOrFail($this->faqId) : new Faq;

        $faq->fill($data);
        $faq->save();

        Flux::toast(
            variant: 'success',
            text: $faq->wasRecentlyCreated ? 'Pertanyaan ditambahkan.' : 'Pertanyaan diperbarui.',
        );

        $this->dispatch('modal-close', name: 'faq');
        $this->resetForm();
    }

    public function moveUp(int $faqId): void
    {
        $this->swapOrder($faqId, -1);
    }

    public function moveDown(int $faqId): void
    {
        $this->swapOrder($faqId, 1);
    }

    /**
     * Swap sort_order with the neighbouring row so the public order changes.
     */
    protected function swapOrder(int $faqId, int $direction): void
    {
        $current = Faq::query()->findOrFail($faqId);

        $neighbour = Faq::query()
            ->when(
                $direction < 0,
                fn ($query) => $query
                    ->where('sort_order', '<', $current->sort_order)
                    ->orderByDesc('sort_order'),
                fn ($query) => $query
                    ->where('sort_order', '>', $current->sort_order)
                    ->orderBy('sort_order'),
            )
            ->first();

        if ($neighbour === null) {
            return;
        }

        Faq::query()->whereKey($current->getKey())->update(['sort_order' => $neighbour->sort_order]);
        Faq::query()->whereKey($neighbour->getKey())->update(['sort_order' => $current->sort_order]);
    }

    public function toggleActive(int $faqId): void
    {
        $faq = Faq::query()->findOrFail($faqId);

        $faq->update(['is_active' => ! $faq->is_active]);

        Flux::toast(
            variant: 'success',
            text: $faq->is_active ? 'Pertanyaan ditayangkan.' : 'Pertanyaan disembunyikan.',
        );
    }

    public function confirmDelete(int $faqId): void
    {
        $this->confirmingId = $faqId;

        $this->dispatch('modal-show', name: 'hapus-faq');
    }

    public function destroy(): void
    {
        Faq::query()->findOrFail($this->confirmingId)->delete();

        Flux::toast(variant: 'success', text: 'Pertanyaan dihapus.');

        $this->confirmingId = null;
        $this->dispatch('modal-close', name: 'hapus-faq');
    }

    #[Computed]
    public function confirmingQuestion(): string
    {
        return $this->confirmingId
            ? (string) Faq::query()->whereKey($this->confirmingId)->value('question')
            : '';
    }

    protected function resetForm(): void
    {
        $this->reset(['faqId', 'question', 'answer', 'group', 'sort_order', 'confirmingId']);

        $this->is_active = true;
        $this->resetValidation();
    }
}; ?>

<div class="space-y-6">
    <x-admin.page-header
        title="FAQ"
        description="Pertanyaan yang sering muncul. Isi di sini juga dipakai sebagai structured data FAQPage di beranda."
    >
        <flux:button variant="primary" icon="plus" type="button" wire:click="create">
            Tambah Pertanyaan
        </flux:button>
    </x-admin.page-header>

    @if ($this->groups !== [])
        <flux:card class="flex flex-wrap items-center gap-2">
            <flux:text size="sm" class="me-1">Filter grup:</flux:text>

            <flux:button
                size="xs"
                :variant="$groupFilter === '' ? 'primary' : 'outline'"
                type="button"
                wire:click="$set('groupFilter', '')"
                :aria-pressed="$groupFilter === '' ? 'true' : 'false'"
            >
                Semua
            </flux:button>

            @foreach ($this->groups as $group)
                <flux:button
                    size="xs"
                    :variant="$groupFilter === $group ? 'primary' : 'outline'"
                    type="button"
                    wire:click="$set('groupFilter', @js($group))"
                    :aria-pressed="$groupFilter === $group ? 'true' : 'false'"
                >
                    {{ $group }}
                </flux:button>
            @endforeach
        </flux:card>
    @endif

    @if ($this->faqs->isEmpty())
        <x-admin.empty-state
            title="Belum ada pertanyaan"
            description="Jawab keraguan calon pelanggan: biaya, izin PLN, garansi, sampai lama pemasangan."
            icon="question-mark-circle"
        >
            <flux:button variant="primary" icon="plus" type="button" wire:click="create">
                Tambah Pertanyaan
            </flux:button>
        </x-admin.empty-state>
    @else
        <ul class="space-y-3" role="list">
            @foreach ($this->faqs as $faq)
                <li
                    wire:key="faq-{{ $faq->id }}"
                    class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-800"
                >
                    <div class="flex flex-wrap items-start gap-3">
                        <div class="flex shrink-0 flex-col gap-0.5">
                            <flux:button size="xs" variant="ghost" icon="chevron-up" square type="button" wire:click="moveUp({{ $faq->id }})">
                                <span class="sr-only">Naikkan urutan {{ $faq->question }}</span>
                            </flux:button>

                            <flux:button size="xs" variant="ghost" icon="chevron-down" square type="button" wire:click="moveDown({{ $faq->id }})">
                                <span class="sr-only">Turunkan urutan {{ $faq->question }}</span>
                            </flux:button>
                        </div>

                        <div class="min-w-52 flex-1 space-y-1.5">
                            <div class="flex flex-wrap items-center gap-2">
                                <flux:heading level="2" size="lg">{{ $faq->question }}</flux:heading>

                                <flux:badge :color="$faq->is_active ? 'green' : 'zinc'" size="sm" inset="left right">
                                    {{ $faq->is_active ? 'Tayang' : 'Sembunyi' }}
                                </flux:badge>

                                @if (filled($faq->group))
                                    <flux:badge color="zinc" size="sm" inset="left right">{{ $faq->group }}</flux:badge>
                                @endif
                            </div>

                            <flux:text size="sm" class="whitespace-pre-line">{{ $faq->answer }}</flux:text>
                        </div>

                        <div class="flex shrink-0 flex-wrap gap-1">
                            <flux:button size="xs" variant="ghost" icon="pencil-square" type="button" wire:click="edit({{ $faq->id }})">
                                Ubah
                            </flux:button>

                            <flux:button size="xs" variant="ghost" icon="eye" type="button" wire:click="toggleActive({{ $faq->id }})">
                                {{ $faq->is_active ? 'Sembunyikan' : 'Tayangkan' }}
                            </flux:button>

                            <flux:button
                                size="xs"
                                variant="ghost"
                                icon="trash"
                                type="button"
                                class="text-red-500"
                                wire:click="confirmDelete({{ $faq->id }})"
                            >
                                Hapus
                            </flux:button>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif

    <flux:modal name="faq" scroll="body" class="min-w-[24rem] max-w-2xl space-y-5">
        <form wire:submit="save" class="space-y-5">
            <div class="space-y-1">
                <flux:heading level="2" size="lg">{{ $faqId ? 'Ubah Pertanyaan' : 'Tambah Pertanyaan' }}</flux:heading>

                <flux:text size="sm">Tulis jawaban yang ringkas dan langsung ke inti agar mudah dibaca di ponsel.</flux:text>
            </div>

            <flux:input
                wire:model.blur="question"
                label="Pertanyaan"
                type="text"
                required
                placeholder="Berapa lama pemasangan PLTS atap rumah?"
            />

            <flux:textarea
                wire:model.blur="answer"
                label="Jawaban"
                rows="5"
                required
                placeholder="Untuk rumah dengan kapasitas 3–5 kWp, pemasangan umumnya selesai dalam 1–2 hari kerja setelah survei."
            />

            <div class="grid gap-4 sm:grid-cols-2">
                <flux:input
                    wire:model.blur="group"
                    label="Grup"
                    description="Dipakai sebagai penyaring di halaman FAQ."
                    type="text"
                    placeholder="Pemasangan"
                    list="daftar-grup-faq"
                />

                <flux:input
                    wire:model.blur="sort_order"
                    label="Urutan"
                    description="Angka kecil tampil lebih dulu."
                    type="number"
                    min="0"
                    step="1"
                />
            </div>

            <datalist id="daftar-grup-faq">
                @foreach ($this->groups as $existing)
                    <option value="{{ $existing }}"></option>
                @endforeach
            </datalist>

            <flux:switch wire:model="is_active" label="Tayangkan pertanyaan ini di situs" />

            <div class="flex gap-2 border-t border-zinc-200 pt-4 dark:border-zinc-700">
                <flux:spacer />

                <flux:modal.close>
                    <flux:button variant="ghost" type="button">Batal</flux:button>
                </flux:modal.close>

                <flux:button variant="primary" type="submit" icon="check" wire:loading.attr="disabled">
                    Simpan
                </flux:button>
            </div>
        </form>
    </flux:modal>

    <x-admin.confirm-delete name="hapus-faq" title="Hapus pertanyaan?">
        “{{ $this->confirmingQuestion }}” akan hilang dari halaman FAQ dan dari structured data.
    </x-admin.confirm-delete>
</div>
