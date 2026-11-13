<?php

namespace App\Filament\Admin\Widgets;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Services\OrderPricing;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected ?string $heading = 'Hari ini';

    protected function getStats(): array
    {
        $today = today();
        $active = Order::active()->count();
        $pickups = Order::whereDate('pickup_date', $today)
            ->whereIn('status', [OrderStatus::Baru->value, OrderStatus::Disahkan->value, OrderStatus::Pickup->value])->count();
        $deliveries = Order::whereDate('delivery_date', $today)->active()->count();
        $workshop = Order::whereIn('status', [OrderStatus::Cucian->value, OrderStatus::Kering->value, OrderStatus::Qc->value, OrderStatus::Sedia->value])
            ->withCount('items')->get();
        $unpaid = Order::notCancelled()->where('payment_status', PaymentStatus::Unpaid->value);
        $month = Order::notCancelled()->where('created_at', '>=', $today->copy()->startOfMonth());

        // Order value per day for the last 14 days, for the sparkline.
        $spark = [];
        $rows = Order::notCancelled()->where('created_at', '>=', $today->copy()->subDays(13))->get(['created_at', 'total']);
        for ($i = 13; $i >= 0; $i--) {
            $d = $today->copy()->subDays($i)->toDateString();
            $spark[] = (float) $rows->filter(fn ($o) => $o->created_at->toDateString() === $d)->sum('total');
        }

        return [
            Stat::make('Order aktif', $active)->description('belum selesai')->icon('heroicon-o-clipboard-document-list'),
            Stat::make('Pickup hari ini', $pickups)->description($today->format('D, j M'))->icon('heroicon-o-arrow-down-tray')->color('info'),
            Stat::make('Delivery hari ini', $deliveries)->description($today->format('D, j M'))->icon('heroicon-o-truck')->color('info'),
            Stat::make('Di bengkel', $workshop->sum('items_count').' karpet')->description($workshop->count().' order')->icon('heroicon-o-sparkles')->color('warning'),
            Stat::make('Belum bayar', OrderPricing::rm((clone $unpaid)->sum('total')))->description((clone $unpaid)->count().' order')->color('warning'),
            Stat::make('Nilai bulan ini', OrderPricing::rm($month->sum('total')))->description('14 hari terakhir')->chart($spark)->color('primary'),
        ];
    }
}
