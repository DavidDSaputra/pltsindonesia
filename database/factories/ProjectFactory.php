<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = Str::title(fake()->unique()->words(5, true));

        return [
            'title' => $title,
            'slug' => Str::slug($title).'-'.Str::lower(Str::random(5)),
            'client' => fake()->company(),
            'location' => fake()->city(),
            'capacity_kwp' => fake()->randomFloat(2, 2, 500),
            'completed_on' => fake()->dateTimeBetween('-4 years', 'now'),
            'summary' => fake()->sentence(18),
            'body' => "## Konfigurasi\n\n".implode("\n\n", fake()->paragraphs(3)),
            'image' => null,
            'is_featured' => false,
            'is_active' => true,
            'sort_order' => 0,
        ];
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => ['is_featured' => true]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => ['is_active' => false]);
    }
}
