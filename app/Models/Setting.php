<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Key/value store behind the CMS "Pengaturan" screen.
 *
 * Values are stored as JSON so a single key can hold a scalar or a nested
 * array (e.g. the "social" key holds every social profile URL).
 */
#[Fillable(['key', 'value'])]
class Setting extends Model
{
    public const string CACHE_KEY = 'settings.all';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'value' => 'array',
        ];
    }

    /**
     * Every setting as a flat key => value map, cached indefinitely.
     *
     * @return array<string, mixed>
     */
    public static function cached(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, fn () => static::query()
            ->get()
            ->pluck('value', 'key')
            ->all());
    }

    /**
     * Read one setting. Dot notation walks into JSON values: site('social.instagram').
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return data_get(static::cached(), $key, $default);
    }

    /**
     * Write one setting and invalidate the cache.
     */
    public static function set(string $key, mixed $value): void
    {
        static::query()->updateOrCreate(['key' => $key], ['value' => $value]);

        static::flush();
    }

    /**
     * Write many settings at once.
     *
     * @param  array<string, mixed>  $values
     */
    public static function setMany(array $values): void
    {
        foreach ($values as $key => $value) {
            static::query()->updateOrCreate(['key' => (string) $key], ['value' => $value]);
        }

        static::flush();
    }

    public static function flush(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
