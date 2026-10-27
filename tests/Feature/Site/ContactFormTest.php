<?php

namespace Tests\Feature\Site;

use App\Enums\MessageStatus;
use App\Models\ContactMessage;
use App\Models\Subscriber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, mixed>
     */
    protected function validPayload(): array
    {
        return [
            'name' => 'Hendra Wijaya',
            'email' => 'hendra@example.com',
            'phone' => '0812-8877-4321',
            'subject' => 'PLTS Atap Rumah',
            'message' => 'Rumah saya berdaya 5.500 VA dengan atap menghadap utara. Berapa kapasitas yang cocok?',
        ];
    }

    public function test_the_contact_page_renders_the_form(): void
    {
        $this->get('/kontak')
            ->assertOk()
            ->assertSee('Kirim pesan', false);
    }

    public function test_a_valid_submission_stores_a_message(): void
    {
        Livewire::test('pages::site.contact-form')
            ->set($this->validPayload())
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('sent', true);

        $this->assertDatabaseCount('contact_messages', 1);

        $message = ContactMessage::query()->firstOrFail();

        $this->assertSame('Hendra Wijaya', $message->name);
        $this->assertSame('PLTS Atap Rumah', $message->subject);
        $this->assertSame(MessageStatus::New, $message->status);
        $this->assertNotNull($message->ip_address);
    }

    public function test_the_form_is_cleared_after_a_successful_submission(): void
    {
        Livewire::test('pages::site.contact-form')
            ->set($this->validPayload())
            ->call('submit')
            ->assertSet('name', '')
            ->assertSet('email', '')
            ->assertSet('message', '');
    }

    public function test_required_fields_are_validated_with_indonesian_messages(): void
    {
        Livewire::test('pages::site.contact-form')
            ->set('name', '')
            ->set('email', 'bukan-email')
            ->set('subject', '')
            ->set('message', 'pendek')
            ->call('submit')
            ->assertHasErrors(['name', 'email', 'subject', 'message']);

        $this->assertDatabaseCount('contact_messages', 0);
    }

    public function test_the_phone_number_is_optional(): void
    {
        Livewire::test('pages::site.contact-form')
            ->set($this->validPayload())
            ->set('phone', '')
            ->call('submit')
            ->assertHasNoErrors();

        $this->assertNull(ContactMessage::query()->firstOrFail()->phone);
    }

    public function test_a_filled_honeypot_silently_discards_the_submission(): void
    {
        Livewire::test('pages::site.contact-form')
            ->set($this->validPayload())
            ->set('company', 'SpamBot Corp')
            ->call('submit')
            ->assertSet('sent', true);

        $this->assertDatabaseCount('contact_messages', 0);
    }

    public function test_submissions_are_rate_limited_per_visitor(): void
    {
        RateLimiter::clear('contact-form:127.0.0.1');

        foreach (range(1, 3) as $attempt) {
            Livewire::test('pages::site.contact-form')
                ->set($this->validPayload())
                ->set('subject', 'Permintaan ke-'.$attempt)
                ->call('submit')
                ->assertHasNoErrors();
        }

        $this->assertDatabaseCount('contact_messages', 3);

        Livewire::test('pages::site.contact-form')
            ->set($this->validPayload())
            ->set('subject', 'Permintaan keempat')
            ->call('submit')
            ->assertHasErrors('message')
            ->assertSet('sent', false);

        $this->assertDatabaseCount('contact_messages', 3);
    }

    public function test_a_visitor_can_subscribe_from_the_footer_form(): void
    {
        Livewire::test('pages::site.newsletter-form')
            ->set('email', 'pembaca@example.com')
            ->set('consent', true)
            ->call('subscribe')
            ->assertHasNoErrors()
            ->assertSet('subscribed', true);

        $subscriber = Subscriber::query()->firstOrFail();

        $this->assertSame('pembaca@example.com', $subscriber->email);
        $this->assertSame('footer', $subscriber->source);
        $this->assertTrue($subscriber->is_active);
    }

    public function test_subscription_requires_consent(): void
    {
        Livewire::test('pages::site.newsletter-form')
            ->set('email', 'pembaca@example.com')
            ->set('consent', false)
            ->call('subscribe')
            ->assertHasErrors('consent');

        $this->assertDatabaseCount('subscribers', 0);
    }

    public function test_subscribing_twice_reactivates_rather_than_duplicates(): void
    {
        $existing = Subscriber::query()->create([
            'email' => 'pembaca@example.com',
            'source' => 'footer',
            'is_active' => false,
            'subscribed_at' => now()->subMonth(),
        ]);

        Livewire::test('pages::site.newsletter-form')
            ->set('email', 'pembaca@example.com')
            ->set('consent', true)
            ->call('subscribe')
            ->assertHasNoErrors();

        $this->assertDatabaseCount('subscribers', 1);
        $this->assertTrue($existing->fresh()->is_active);
    }
}
