<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Seed only the minimum account needed to access the CMS.
 *
 * Public content is intentionally not seeded. Editors add it from the CMS
 * after deployment, so a fresh hosting install starts with an empty site.
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
        ]);
    }
}
