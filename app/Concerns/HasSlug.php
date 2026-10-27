<?php

namespace App\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Generates a URL-safe slug from a source attribute the first time a model is saved.
 *
 * Existing slugs are never rewritten automatically so that published URLs stay stable
 * (important for SEO). Regenerate a slug explicitly when the title really changes.
 *
 * @phpstan-require-extends \Illuminate\Database\Eloquent\Model
 *
 * @property string|null $slug
 */
trait HasSlug
{
    public static function bootHasSlug(): void
    {
        static::saving(function (Model $model) {
            /** @var Model&HasSlug $model */
            if (blank($model->slug)) {
                $model->slug = $model->generateUniqueSlug();
            }
        });
    }

    /**
     * The attribute the slug is derived from.
     */
    public function slugSource(): string
    {
        return property_exists($this, 'slugFrom') ? $this->slugFrom : 'title';
    }

    /**
     * Build a slug that is unique within the table, including soft-deleted rows.
     */
    public function generateUniqueSlug(?string $from = null): string
    {
        $base = Str::slug((string) ($from ?? $this->{$this->slugSource()} ?? ''));

        if ($base === '') {
            $base = Str::lower(Str::random(10));
        }

        $slug = $base;
        $suffix = 2;

        while ($this->slugIsTaken($slug)) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }

    /**
     * Check the table directly so global scopes (e.g. soft deletes) cannot hide a collision.
     */
    protected function slugIsTaken(string $slug): bool
    {
        return DB::table($this->getTable())
            ->where('slug', $slug)
            ->when($this->exists, fn ($query) => $query->where('id', '!=', $this->getKey()))
            ->exists();
    }

    /**
     * Rebuild the slug from the given (or current) source value.
     */
    public function regenerateSlug(?string $from = null): static
    {
        $this->slug = $this->generateUniqueSlug($from);

        return $this;
    }
}
