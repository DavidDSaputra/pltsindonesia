<?php

namespace Database\Factories;

use App\Models\Faq;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Faq>
 */
class FaqFactory extends Factory
{
    /**
     * Groups used by the public FAQ page to build its jump links.
     */
    protected const array GROUPS = ['biaya', 'teknis', 'perizinan', 'perawatan'];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'question' => rtrim(fake()->unique()->sentence(8), '.').'?',
            // Rendered as a single paragraph, so no markdown here.
            'answer' => fake()->paragraph(3),
            'group' => fake()->randomElement(self::GROUPS),
            'is_active' => true,
            'sort_order' => 0,
        ];
    }

    public function group(string $group): static
    {
        return $this->state(fn (array $attributes) => ['group' => $group]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => ['is_active' => false]);
    }
}
