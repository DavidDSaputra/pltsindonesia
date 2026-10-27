<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Icon names that both the public icon component and the CMS picker accept.
     */
    protected const array ICONS = ['home', 'building', 'bolt', 'battery', 'wrench', 'chart'];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = Str::title(fake()->unique()->words(4, true));

        return [
            'title' => $title,
            'slug' => Str::slug($title).'-'.Str::lower(Str::random(5)),
            'icon' => fake()->randomElement(self::ICONS),
            'summary' => fake()->sentence(16),
            'body' => "## Gambaran layanan\n\n".implode("\n\n", fake()->paragraphs(3)),
            'image' => null,
            'features' => fake()->sentences(4),
            'is_active' => true,
            'sort_order' => 0,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => ['is_active' => false]);
    }
}
