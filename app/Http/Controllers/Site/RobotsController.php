<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

class RobotsController extends Controller
{
    /**
     * Generated on the fly so the Sitemap directive always matches APP_URL.
     */
    public function __invoke(): Response
    {
        $blocked = [
            '/admin',
            '/dashboard',
            '/settings',
            '/login',
            '/register',
            '/forgot-password',
            '/reset-password',
            '/verify-email',
            '/confirm-password',
            '/two-factor-challenge',
            '/*?cari=',
            '/*?filter=',
            '/*?page=',
        ];

        $lines = array_merge(
            ['User-agent: *'],
            array_map(fn (string $path): string => 'Disallow: '.$path, $blocked),
            [
                '',
                'Allow: /sitemap.xml',
                'Allow: /feed.xml',
                '',
                'Sitemap: '.url('/sitemap.xml'),
                '',
            ]
        );

        return response(implode(PHP_EOL, $lines), 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}
