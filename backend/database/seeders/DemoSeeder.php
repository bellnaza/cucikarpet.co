<?php

namespace Database\Seeders;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\UserRole;
use App\Models\CarpetType;
use App\Models\Team;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Database\Seeder;

/**
 * Sample data for trying the system locally. Never run this on the live server:
 *   php artisan db:seed --class=DemoSeeder
 */
class DemoSeeder extends Seeder
{
    public function run(OrderService $orders): void
    {
        if (app()->isProduction()) {
            $this->command?->error('DemoSeeder is for local testing only.');

            return;
        }

        $t = CarpetType::pluck('id', 'code');
        $a = Team::where('name', 'Team A')->first();
        $b = Team::where('name', 'Team B')->first();

        if (env('DEMO_DRIVER_EMAIL') && env('DEMO_DRIVER_PASSWORD')) {
            User::firstOrCreate(['email' => env('DEMO_DRIVER_EMAIL')], [
                'name' => 'Driver Team A (contoh)', 'password' => env('DEMO_DRIVER_PASSWORD'),
                'role' => UserRole::Driver, 'team_id' => $a->id,
            ]);
        }

        $make = function (string $name, string $area, string $addr, array $items, array $attrs) use ($orders) {
            return $orders->create(
                ['name' => $name.' (contoh)', 'phone' => '012-000 '.random_int(1000, 9999), 'email' => strtolower(strtok($name, ' ')).'@example.com', 'address' => $addr, 'area' => $area],
                array_map(fn ($i) => ['carpet_type_id' => $i[0], 'width_ft' => $i[1], 'length_ft' => $i[2]], $items),
                $attrs + ['source' => 'admin'],
                notify: false,
            );
        };

        $o1 = $make('Aminah Yusof', 'Selangor', 'No. 12, Jalan SS2/24, Petaling Jaya', [[$t['standard'], 5, 7], [$t['shaggy'], 6, 9]],
            ['pickup_date' => today()->subDays(2), 'pickup_slot' => 'Pagi (9–12)', 'team_id' => $a->id]);
        $orders->changeStatus($o1, OrderStatus::Diambil, notify: false);
        $orders->changeStatus($o1->fresh(), OrderStatus::Cucian, notify: false);

        $make('Rahman Ali', 'Kuala Lumpur', 'Blok C-5-3, Pangsapuri Seri Setapak, Setapak', [[$t['velvet'], 4, 6]],
            ['pickup_date' => today(), 'pickup_slot' => 'Pagi (9–12)', 'team_id' => $b->id]);

        $o3 = $make('Mei Ling', 'Kuala Lumpur', '18, Jalan Kemuning, Taman Tun Dr Ismail', [[$t['wool'], 5, 8], [$t['standard'], 6, 9], [$t['standard'], 4, 6]],
            ['pickup_date' => today()->subDays(6), 'pickup_slot' => 'Pagi (9–12)', 'team_id' => $a->id]);
        foreach ([OrderStatus::Diambil, OrderStatus::Cucian, OrderStatus::Kering, OrderStatus::Qc, OrderStatus::Sedia] as $s) {
            $orders->changeStatus($o3->fresh(), $s, notify: false);
        }
        $o3->update(['delivery_date' => today()]);

        $o4 = $make('Siva Kumar', 'Selangor', '7, Jalan Kenari 3, Puchong', [[$t['standard'], 8, 10]],
            ['pickup_date' => today()->subDays(9), 'pickup_slot' => 'Petang (2–5)', 'team_id' => $b->id]);
        foreach ([OrderStatus::Diambil, OrderStatus::Cucian, OrderStatus::Kering, OrderStatus::Qc, OrderStatus::Sedia, OrderStatus::Hantar] as $s) {
            $orders->changeStatus($o4->fresh(), $s, notify: false);
        }
        $orders->completeDelivery($o4->fresh(), PaymentMethod::DuitNow);

        $make('Nurul Huda', 'Selangor', '22, Jalan Bukit Indah 2, Ampang', [[$t['shaggy'], 5, 7]],
            ['pickup_date' => today()->addDays(2), 'pickup_slot' => 'Petang (2–5)']);
    }
}
