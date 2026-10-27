<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Newsletter subscriber collected from the public site.
 *
 * @property Carbon|null $subscribed_at
 */
#[Fillable(['email', 'source', 'is_active', 'subscribed_at'])]
class Subscriber extends Model
{
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'subscribed_at' => 'datetime',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderByDesc('created_at');
    }

    /**
     * Subscribe an address, re-activating it when it unsubscribed before.
     */
    public static function subscribe(string $email, ?string $source = null): static
    {
        return static::query()->updateOrCreate(
            ['email' => $email],
            [
                'is_active' => true,
                'source' => $source,
                'subscribed_at' => now(),
            ],
        );
    }
}
