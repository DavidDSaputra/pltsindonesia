<?php

namespace App\Models;

use App\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * A service offering displayed on the public site and managed from the CMS.
 */
#[Fillable([
    'title', 'slug', 'icon', 'summary', 'body', 'image',
    'features', 'is_active', 'sort_order',
])]
class Service extends Model
{
    use HasFactory, HasSlug;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'features' => 'array',
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

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('title');
    }

    public function getImageUrlAttribute(): ?string
    {
        return image_url($this->image);
    }

    public function getBodyHtmlAttribute(): string
    {
        return Str::markdown((string) $this->body);
    }

    public function getMetaDescriptionAttribute(): string
    {
        return Str::limit(trim(strip_tags((string) ($this->summary ?: $this->body))), 160, '');
    }
}
