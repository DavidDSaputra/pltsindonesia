<?php

namespace Database\Factories;

use App\Models\Media;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Media>
 *
 * Rows created here are metadata only — no file is written to disk, so tests
 * must not assert on the stored bytes.
 */
class MediaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = Str::slug(fake()->words(3, true)).'-'.Str::lower(Str::random(6));

        return [
            'user_id' => null,
            'disk' => 'public',
            'path' => 'media/'.now()->format('Y/m').'/'.$name.'.jpg',
            'original_name' => $name.'.jpg',
            'mime_type' => 'image/jpeg',
            'size' => fake()->numberBetween(20_000, 4_000_000),
            'title' => Str::headline($name),
            'alt' => fake()->sentence(8),
        ];
    }

    public function svg(): static
    {
        return $this->state(fn (array $attributes) => [
            'path' => Str::beforeLast($attributes['path'], '.').'.svg',
            'original_name' => Str::beforeLast($attributes['original_name'], '.').'.svg',
            'mime_type' => 'image/svg+xml',
        ]);
    }
}
