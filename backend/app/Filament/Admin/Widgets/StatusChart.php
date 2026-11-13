<?php

namespace App\Filament\Admin\Widgets;

use App\Enums\OrderStatus;
use App\Filament\Admin\RangedChart;
use App\Models\Order;

class StatusChart extends RangedChart
{
    protected static ?string $heading = 'Order aktif ikut status';

    protected static ?string $description = 'Di mana karpet sekarang';

    protected static ?int $sort = 4;

    // Live state, not a period: no range filter.
    protected function getFilters(): ?array
    {
        return null;
    }

    protected function getData(): array
    {
        $counts = Order::active()->selectRaw('status, COUNT(*) as n')->groupBy('status')->pluck('n', 'status');

        $labels = [];
        $values = [];
        foreach (OrderStatus::flow() as $s) {
            if ($s === OrderStatus::Selesai || ! ($counts[$s->value] ?? 0)) {
                continue;
            }
            $labels[] = $s->getLabel();
            $values[] = (int) $counts[$s->value];
        }

        return $this->bars($labels, $values, 'Order');
    }
}
