<?php

namespace App\Models;

use App\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * A completed solar installation shown in the portfolio.
 *
 * @property Carbon|null $completed_on
 * @property float|null $capacity_kwp
 */
#[Fillable([
    'title', 'slug', 'client', 'location', 'capacity_kwp', 'completed_on',
    'summary', 'body', 'image', 'is_featured', 'is_active', 'sort_order',
])]
class Project extends Model
{
    use HasFactory, HasSlug;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'capacity_kwp' => 'decimal:2',
            'completed_on' => 'date',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderByDesc('completed_on')->orderBy('sort_order');
    }

    public function getImageUrlAttribute(): ?string
    {
        return image_url($this->image);
    }

    public function getBodyHtmlAttribute(): string
    {
        return Str::markdown((string) $this->body);
    }

    /**
     * Capacity formatted for display, e.g. "125 kWp".
     */
    public function getCapacityLabelAttribute(): ?string
    {
        if ($this->capacity_kwp === null) {
            return null;
        }

        return number_format((float) $this->capacity_kwp, 0, ',', '.').' kWp';
    }

    public function getMetaDescriptionAttribute(): string
    {
        return Str::limit(trim(strip_tags((string) ($this->summary ?: $this->body))), 160, '');
    }
}
