<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Portfolio photo shown in the public gallery grid.
 */
#[Fillable(['title', 'image', 'alt', 'group', 'is_active', 'sort_order'])]
class Gallery extends Model
{
    use HasFactory;

    protected $table = 'galleries';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
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

    public function getImageUrlAttribute(): ?string
    {
        return image_url($this->image);
    }

    /**
     * Alt text with a fallback so every image stays accessible.
     */
    public function getAltTextAttribute(): string
    {
        return filled($this->alt) ? (string) $this->alt : (string) ($this->title ?? 'Dokumentasi proyek panel surya');
    }

    /**
     * Distinct, non-empty group labels for the gallery filter.
     *
     * @return array<int, string>
     */
    public static function groups(): array
    {
        return static::query()
            ->active()
            ->whereNotNull('group')
            ->where('group', '!=', '')
            ->distinct()
            ->orderBy('group')
            ->pluck('group')
            ->all();
    }
}
