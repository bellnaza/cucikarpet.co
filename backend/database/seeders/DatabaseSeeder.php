<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\CarpetType;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class DatabaseSeeder extends Seeder
{
    /** Safe to run in production: prices, teams, and the first admin account. */
    public function run(): void
    {
        $types = [
            ['code' => 'wool', 'name' => 'Handmade / Wool / Silk', 'name_en' => 'Handmade / Wool / Silk', 'rate_per_sqft' => 5.00, 'sort' => 1,
                'description' => 'Karpet tenunan tangan, bulu dan sutera.'],
            ['code' => 'shaggy', 'name' => 'Shaggy', 'name_en' => 'Shaggy', 'rate_per_sqft' => 3.50, 'sort' => 2,
                'description' => 'Bulu panjang dan tebal.'],
            ['code' => 'standard', 'name' => 'Standard / Turki', 'name_en' => 'Standard / Turkish', 'rate_per_sqft' => 3.20, 'sort' => 3,
                'description' => 'Karpet mesin biasa dan karpet Turki.'],
            ['code' => 'velvet', 'name' => 'Velvet / Tatami', 'name_en' => 'Velvet / Tatami', 'rate_per_sqft' => 2.00, 'sort' => 4,
                'description' => 'Karpet nipis, velvet dan tikar tatami.'],
        ];
        foreach ($types as $t) {
            CarpetType::updateOrCreate(['code' => $t['code']], $t);
        }

        Team::firstOrCreate(['name' => 'Team A']);
        Team::firstOrCreate(['name' => 'Team B']);

        $email = env('ADMIN_EMAIL');
        $password = env('ADMIN_PASSWORD');
        if (! $email || ! $password) {
            throw new RuntimeException('Set ADMIN_EMAIL and ADMIN_PASSWORD in .env before seeding.');
        }

        User::firstOrCreate(['email' => $email], [
            'name' => env('ADMIN_NAME', 'Admin cucikarpet.co'),
            'password' => $password,
            'role' => UserRole::Admin,
        ]);
    }
}
