<?php

use App\Models\Setting;
use Illuminate\Support\Facades\Storage;

if (! function_exists('site')) {
    /**
     * Read a site setting from the CMS-managed settings table.
     *
     * Supports dot notation into JSON values, e.g. site('social.instagram').
     */
    function site(string $key, mixed $default = null): mixed
    {
        return Setting::get($key, $default);
    }
}

if (! function_exists('image_url')) {
    /**
     * Resolve a stored image reference to a usable URL.
     *
     * Accepts absolute URLs, root-relative paths (public/images/...) and
     * filesystem paths relative to the public disk (articles/cover.jpg).
     */
    function image_url(?string $path, ?string $fallback = null): ?string
    {
        if (blank($path)) {
            return $fallback;
        }

        if (preg_match('#^(https?:)?//#', $path) === 1 || str_starts_with($path, '/')) {
            return $path;
        }

        return Storage::disk('public')->url($path);
    }
}
