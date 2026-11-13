<?php

namespace App\Filament\Admin\Widgets;

use App\Filament\Admin\RangedChart;

class AreaChart extends RangedChart
{
    protected static ?string $heading = 'Kawasan (bilangan order)';

    protected static ?int $sort = 5;

    protected function getData(): array
    {
        $rows = $this->ordersInRange()->with('customer:id,area')->get(['id', 'customer_id'])
            ->groupBy(fn ($o) => $o->customer?->area ?: 'Lain-lain')
            ->map->count()->sortDesc();

        return $this->bars($rows->keys()->all(), $rows->values()->all(), 'Order');
    }
}
