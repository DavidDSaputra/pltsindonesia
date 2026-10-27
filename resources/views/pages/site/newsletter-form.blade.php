<?php

use App\Models\Subscriber;
use Livewire\Attributes\Rule;
use Livewire\Component;

new class extends Component
{
    #[Rule(['required', 'email:filter', 'max:255'])]
    public string $email = '';

    #[Rule(['accepted'])]
    public bool $consent = false;

    public bool $subscribed = false;

    public function subscribe(): void
    {
        $this->validate();

        Subscriber::subscribe($this->email, source: 'footer');

        $this->reset(['email', 'consent']);

        $this->subscribed = true;
    }

    public function updatedEmail(): void
    {
        $this->subscribed = false;
    }
}; ?>

<section aria-labelledby="newsletter-heading" class="rounded-2xl bg-white/5 p-5 ring-1 ring-white/15">
    <h3 id="newsletter-heading" class="text-sm font-bold text-white">Info Energi Surya</h3>

    <p class="mt-2 text-xs leading-relaxed text-night-200">
        Dapatkan artikel edukasi, tips penghematan listrik, dan promo pemasangan PLTS langsung ke email Anda.
    </p>

    @if ($subscribed)
        <p role="status" class="mt-4 flex items-start gap-2 rounded-xl bg-solar-400/15 p-3 text-xs font-medium text-solar-200 ring-1 ring-solar-400/30">
            <x-site.icon name="check-circle" class="size-4 shrink-0" stroke="2" />
            <span>Terima kasih! Email Anda sudah terdaftar.</span>
        </p>
    @else
        <form wire:submit="subscribe" novalidate class="mt-4 grid gap-3">
            <div>
                <label for="newsletter-email" class="sr-only">Alamat email</label>

                <input
                    id="newsletter-email"
                    type="email"
                    name="email"
                    wire:model="email"
                    autocomplete="email"
                    placeholder="nama@email.com"
                    aria-describedby="newsletter-error"
                    class="w-full rounded-xl border-0 bg-white/10 px-3.5 py-2.5 text-sm text-white placeholder:text-night-300 ring-1 ring-white/20 ring-inset transition focus:ring-2 focus:ring-solar-400 focus:outline-none"
                />

                @error('email')
                    <p id="newsletter-error" role="alert" class="mt-2 text-xs font-medium text-solar-300">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="newsletter-consent" class="flex items-start gap-2.5 text-xs text-night-200">
                    <input
                        id="newsletter-consent"
                        type="checkbox"
                        name="consent"
                        wire:model="consent"
                        class="mt-0.5 size-4 shrink-0 rounded border-white/30 bg-white/10 text-solar-500 accent-solar-500 focus:ring-2 focus:ring-solar-400 focus:ring-offset-0"
                    />
                    <span>Saya setuju menerima email berkala dan dapat berhenti kapan saja.</span>
                </label>

                @error('consent')
                    <p role="alert" class="mt-2 text-xs font-medium text-solar-300">Centang persetujuan untuk melanjutkan.</p>
                @enderror
            </div>

            <button
                type="submit"
                wire:loading.attr="disabled"
                class="inline-flex items-center justify-center gap-2 rounded-full bg-solar-500 px-4 py-2.5 text-sm font-semibold text-night-950 transition-colors hover:bg-solar-400 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-solar-400 disabled:opacity-60"
            >
                <span wire:loading.remove wire:target="subscribe">Berlangganan</span>
                <span wire:loading wire:target="subscribe">Memproses…</span>
                <x-site.icon name="arrow-right" class="size-4" stroke="2" wire:loading.remove wire:target="subscribe" />
            </button>
        </form>
    @endif
</section>
