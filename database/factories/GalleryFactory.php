<?php

namespace Database\Factories;

use App\Models\Gallery;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Gallery>
 */
class GalleryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = Str::title(fake()->words(4, true));

        return [
            'title' => $title,
            // The image column is not nullable, so factories always supply one.
            'image' => '/images/'.Str::slug($title).'-'.Str::lower(Str::random(6)).'.svg',
            'alt' => fake()->sentence(10),
            'group' => fake()->randomElement(['Rumah Tinggal', 'Proses Pemasangan', 'Komersial & Industri', 'Perawatan']),
            'is_active' => true,
            'sort_order' => 0,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => ['is_active' => false]);
    }
}
