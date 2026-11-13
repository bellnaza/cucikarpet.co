<?php

namespace App\Filament\Admin\Widgets;

use App\Filament\Admin\RangedChart;
use App\Models\CarpetType;
use App\Models\OrderItem;

class CarpetTypeChart extends RangedChart
{
    protected static ?string $heading = 'Jenis karpet (sqft dicuci)';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $orderIds = $this->ordersInRange()->pluck('id');
        $sums = OrderItem::whereIn('order_id', $orderIds)
            ->selectRaw('carpet_type_id, SUM(sqft) as sqft')->groupBy('carpet_type_id')->pluck('sqft', 'carpet_type_id');

        $types = CarpetType::orderBy('sort')->get()
            ->map(fn ($t) => ['l' => $t->name, 'v' => round((float) ($sums[$t->id] ?? 0), 2)])
            ->sortByDesc('v')->values();

        return $this->bars($types->pluck('l')->all(), $types->pluck('v')->all(), 'sqft');
    }
}
