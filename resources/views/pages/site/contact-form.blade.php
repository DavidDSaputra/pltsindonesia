<?php

use App\Models\ContactMessage;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Rule;
use Livewire\Component;

new class extends Component
{
    #[Rule(['required', 'string', 'min:3', 'max:120'])]
    public string $name = '';

    #[Rule(['required', 'email:filter', 'max:180'])]
    public string $email = '';

    #[Rule(['nullable', 'string', 'max:30'])]
    public string $phone = '';

    #[Rule(['required', 'string', 'min:3', 'max:160'])]
    public string $subject = '';

    #[Rule(['required', 'string', 'min:10', 'max:3000'])]
    public string $message = '';

    /** Honeypot field: must stay empty, bots usually fill it in. */
    public string $company = '';

    public bool $sent = false;

    public function submit(): void
    {
        $this->validate(attributes: [
            'name' => 'nama',
            'email' => 'email',
            'phone' => 'nomor telepon',
            'subject' => 'subjek',
            'message' => 'pesan',
        ]);

        // Silent success for bots so they never learn the field was a trap.
        if (filled($this->company)) {
            $this->sent = true;

            return;
        }

        $throttleKey = 'contact-form:'.request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $this->addError('message', 'Terlalu banyak pesan dikirim. Silakan coba lagi '.RateLimiter::availableIn($throttleKey).' detik lagi.');

            return;
        }

        RateLimiter::hit($throttleKey, 3600);

        ContactMessage::create([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone ?: null,
            'subject' => $this->subject,
            'message' => $this->message,
            'ip_address' => request()->ip(),
        ]);

        $this->reset(['name', 'email', 'phone', 'subject', 'message']);

        $this->sent = true;
    }

    public function updated(): void
    {
        $this->sent = false;
    }
}; ?>

<form wire:submit="submit" novalidate class="grid gap-5 rounded-2xl bg-white p-6 ring-1 ring-night-900/10 sm:p-8">
    <div>
        <h2 class="text-xl font-bold tracking-tight text-night-950 sm:text-2xl">Kirim pesan</h2>
        <p class="mt-1.5 text-sm text-zinc-600">Isi formulir di bawah ini dan tim kami akan membalas maksimal 1×24 jam kerja.</p>
    </div>

    @if ($sent)
        <p role="status" class="flex items-start gap-3 rounded-xl bg-solar-50 p-4 text-sm font-medium text-solar-900 ring-1 ring-solar-200">
            <x-site.icon name="check-circle" class="size-5 shrink-0 text-solar-600" stroke="2" />
            <span>Terima kasih, pesan Anda sudah kami terima. Tim kami akan segera menghubungi Anda.</span>
        </p>
    @endif

    <div class="grid gap-5 sm:grid-cols-2">
        <div>
            <label for="kontak-nama" class="mb-1.5 block text-sm font-semibold text-night-900">
                Nama lengkap <span class="text-solar-700" aria-hidden="true">*</span>
            </label>

            <input
                id="kontak-nama"
                type="text"
                wire:model="name"
                autocomplete="name"
                required
                aria-required="true"
                @error('name') aria-invalid="true" aria-describedby="error-name" @enderror
                class="w-full rounded-xl border-0 bg-white px-4 py-2.5 text-sm text-night-950 ring-1 ring-night-900/15 ring-inset transition placeholder:text-zinc-400 focus:ring-2 focus:ring-solar-500 focus:outline-none"
                placeholder="Nama Anda"
            />

            @error('name')
                <p id="error-name" role="alert" class="mt-1.5 text-xs font-medium text-red-700">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="kontak-email" class="mb-1.5 block text-sm font-semibold text-night-900">
                Email <span class="text-solar-700" aria-hidden="true">*</span>
            </label>

            <input
                id="kontak-email"
                type="email"
                wire:model="email"
                autocomplete="email"
                required
                aria-required="true"
                @error('email') aria-invalid="true" aria-describedby="error-email" @enderror
                class="w-full rounded-xl border-0 bg-white px-4 py-2.5 text-sm text-night-950 ring-1 ring-night-900/15 ring-inset transition placeholder:text-zinc-400 focus:ring-2 focus:ring-solar-500 focus:outline-none"
                placeholder="nama@email.com"
            />

            @error('email')
                <p id="error-email" role="alert" class="mt-1.5 text-xs font-medium text-red-700">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="kontak-telepon" class="mb-1.5 block text-sm font-semibold text-night-900">Nomor WhatsApp</label>

            <input
                id="kontak-telepon"
                type="tel"
                wire:model="phone"
                autocomplete="tel"
                @error('phone') aria-invalid="true" aria-describedby="error-phone" @enderror
                class="w-full rounded-xl border-0 bg-white px-4 py-2.5 text-sm text-night-950 ring-1 ring-night-900/15 ring-inset transition placeholder:text-zinc-400 focus:ring-2 focus:ring-solar-500 focus:outline-none"
                placeholder="0812 3456 7890"
            />

            @error('phone')
                <p id="error-phone" role="alert" class="mt-1.5 text-xs font-medium text-red-700">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="kontak-subjek" class="mb-1.5 block text-sm font-semibold text-night-900">
                Kebutuhan Anda <span class="text-solar-700" aria-hidden="true">*</span>
            </label>

            <select
                id="kontak-subjek"
                wire:model="subject"
                required
                aria-required="true"
                @error('subject') aria-invalid="true" aria-describedby="error-subject" @enderror
                class="w-full rounded-xl border-0 bg-white px-4 py-2.5 text-sm text-night-950 ring-1 ring-night-900/15 ring-inset transition focus:ring-2 focus:ring-solar-500 focus:outline-none"
            >
                <option value="">Pilih kebutuhan</option>
                <option value="PLTS Atap Rumah">PLTS Atap Rumah</option>
                <option value="PLTS Bisnis & Industri">PLTS Bisnis &amp; Industri</option>
                <option value="Pompa Air Tenaga Surya">Pompa Air Tenaga Surya</option>
                <option value="PJU Tenaga Surya">PJU Tenaga Surya</option>
                <option value="Perawatan & Perbaikan">Perawatan &amp; Perbaikan</option>
                <option value="Lainnya">Lainnya</option>
            </select>

            @error('subject')
                <p id="error-subject" role="alert" class="mt-1.5 text-xs font-medium text-red-700">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div>
        <label for="kontak-pesan" class="mb-1.5 block text-sm font-semibold text-night-900">
            Pesan <span class="text-solar-700" aria-hidden="true">*</span>
        </label>

        <textarea
            id="kontak-pesan"
            wire:model="message"
            rows="5"
            required
            aria-required="true"
            @error('message') aria-invalid="true" aria-describedby="error-message" @enderror
            class="w-full rounded-xl border-0 bg-white px-4 py-3 text-sm text-night-950 ring-1 ring-night-900/15 ring-inset transition placeholder:text-zinc-400 focus:ring-2 focus:ring-solar-500 focus:outline-none"
            placeholder="Ceritakan lokasi, daya listrik terpasang, dan target Anda."
        ></textarea>

        @error('message')
            <p id="error-message" role="alert" class="mt-1.5 text-xs font-medium text-red-700">{{ $message }}</p>
        @enderror
    </div>

    {{-- Honeypot, hidden from humans and assistive tech alike. --}}
    <div class="hidden" aria-hidden="true">
        <label for="kontak-perusahaan">Perusahaan</label>
        <input id="kontak-perusahaan" type="text" wire:model="company" tabindex="-1" autocomplete="off" />
    </div>

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <p class="text-xs text-zinc-500">
            <span class="text-solar-700" aria-hidden="true">*</span> wajib diisi. Data Anda hanya dipakai untuk menindaklanjuti permintaan ini.
        </p>

        <button
            type="submit"
            wire:loading.attr="disabled"
            class="inline-flex items-center justify-center gap-2 rounded-full bg-night-950 px-6 py-3 text-sm font-semibold text-white transition-colors hover:bg-night-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-night-700 disabled:opacity-60"
        >
            <span wire:loading.remove wire:target="submit">Kirim Pesan</span>
            <span wire:loading wire:target="submit">Mengirim…</span>
            <x-site.icon name="arrow-right" class="size-4" stroke="2" wire:loading.remove wire:target="submit" />
        </button>
    </div>
</form>
