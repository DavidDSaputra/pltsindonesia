<?php

namespace Database\Factories;

use App\Enums\MessageStatus;
use App\Models\ContactMessage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ContactMessage>
 */
class ContactMessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => '08'.fake()->numberBetween(11, 89).fake()->numerify('########'),
            'subject' => fake()->randomElement([
                'Permintaan penawaran PLTS atap',
                'Konsultasi kapasitas sistem',
                'Kerja sama perawatan PLTS',
                'Pertanyaan garansi panel',
                null,
            ]),
            'message' => fake()->paragraph(4),
            'status' => MessageStatus::New,
            'ip_address' => fake()->ipv4(),
            'created_at' => fake()->dateTimeBetween('-3 months', 'now'),
        ];
    }

    public function status(MessageStatus $status): static
    {
        return $this->state(fn (array $attributes) => ['status' => $status]);
    }
}
