<?php

namespace Database\Factories;

use App\Models\Testimonial;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Testimonial>
 */
class TestimonialFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->name(),
            'role' => fake()->randomElement(['Pemilik Rumah', 'Direktur Operasi', 'Manajer Fasilitas', 'Pemilik Usaha']),
            'company' => fake()->company(),
            'avatar' => null,
            'quote' => fake()->paragraph(2),
            'rating' => fake()->numberBetween(4, 5),
            'is_active' => true,
            'sort_order' => 0,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => ['is_active' => false]);
    }
}
