<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\CarpetType;
use App\Models\Customer;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

abstract class CkTestCase extends TestCase
{
    use RefreshDatabase;

    protected array $type = [];

    protected function setUp(): void
    {
        parent::setUp();

        foreach ([['wool', 'Handmade / Wool / Silk', 5], ['shaggy', 'Shaggy', 3.5], ['standard', 'Standard / Turki', 3.2], ['velvet', 'Velvet / Tatami', 2]] as $i => [$code, $name, $rate]) {
            $this->type[$code] = CarpetType::create(['code' => $code, 'name' => $name, 'rate_per_sqft' => $rate, 'sort' => $i])->id;
        }
    }

    protected function carpet(string $code, float $w, float $l): array
    {
        return ['carpet_type_id' => $this->type[$code], 'width_ft' => $w, 'length_ft' => $l];
    }

    protected function customer(?string $email = 'pelanggan@example.com'): Customer
    {
        return Customer::create(['name' => 'Pelanggan Ujian', 'phone' => '012-345 6789', 'email' => $email, 'address' => '1, Jalan Ujian, KL', 'area' => 'Kuala Lumpur']);
    }

    protected function user(UserRole $role, ?Team $team = null): User
    {
        return User::create([
            'name' => $role->value.' ujian', 'email' => $role->value.uniqid().'@example.com',
            'password' => 'password-ujian-123', 'role' => $role, 'team_id' => $team?->id,
        ]);
    }
}
