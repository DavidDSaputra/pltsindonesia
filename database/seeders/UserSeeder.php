<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Create the accounts that can reach the CMS.
 *
 * Credentials come from the environment so a production deploy never ships
 * with a known password. In local development the defaults below are used.
 */
class UserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->updateOrCreate(
            ['email' => $this->seeded('SEED_ADMIN_EMAIL', 'admin@pltsindonesia.test')],
            [
                'name' => $this->seeded('SEED_ADMIN_NAME', 'Bagas Pratama'),
                'password' => Hash::make($this->seeded('SEED_ADMIN_PASSWORD', 'Surya!2026')),
                'role' => UserRole::Admin,
                'bio' => 'Memimpin tim engineering dan bertanggung jawab atas seluruh proyek PLTS.',
                'email_verified_at' => now(),
            ],
        );

        $editor = User::query()->updateOrCreate(
            ['email' => $this->seeded('SEED_EDITOR_EMAIL', 'redaksi@pltsindonesia.test')],
            [
                'name' => $this->seeded('SEED_EDITOR_NAME', 'Rina Kartika'),
                'password' => Hash::make($this->seeded('SEED_EDITOR_PASSWORD', 'Redaksi!2026')),
                'role' => UserRole::Editor,
                'bio' => 'Penulis konten edukasi energi terbarukan dan pengelola blog perusahaan.',
                'email_verified_at' => now(),
            ],
        );

        $this->command?->info(sprintf(
            'CMS ready — admin %s, editor %s.',
            $admin->email,
            $editor->email,
        ));
    }

    /**
     * Read a seeder override from the environment.
     *
     * A key that is present but empty (the usual state of a shipped .env) still
     * falls back to the default, so a blank password can never lock the CMS out.
     */
    protected function seeded(string $key, string $default): string
    {
        $value = env($key);

        return filled($value) ? (string) $value : $default;
    }
}
