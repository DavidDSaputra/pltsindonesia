<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Customer testimonial shown in the reviews carousel.
 */
#[Fillable([
    'name', 'role', 'company', 'avatar', 'quote',
    'rating', 'is_active', 'sort_order',
])]
class Testimonial extends Model
{
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderByDesc('id');
    }

    public function getAvatarUrlAttribute(): ?string
    {
        return image_url($this->avatar);
    }

    /**
     * Name plus company, used as the visible attribution line.
     */
    public function getAttributionAttribute(): string
    {
        return collect([$this->role, $this->company])
            ->filter()
            ->implode(' · ');
    }
}
