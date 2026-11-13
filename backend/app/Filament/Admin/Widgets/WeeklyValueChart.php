<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;

class WeeklyValueChart extends ChartWidget
{
    protected static ?string $heading = 'Nilai tempahan mingguan (RM)';

    protected static ?string $description = '8 minggu terakhir, ikut tarikh order dicipta, tidak termasuk batal';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $maxHeight = '280px';

    protected function getData(): array
    {
        $start = today()->startOfWeek()->subWeeks(7);
        $orders = Order::notCancelled()->where('created_at', '>=', $start)->get(['created_at', 'total']);

        $labels = [];
        $values = [];
        for ($w = 0; $w < 8; $w++) {
            $from = $start->copy()->addWeeks($w);
            $to = $from->copy()->addWeek();
            $labels[] = $w === 7 ? 'Minggu ini' : $from->format('j M');
            $values[] = round((float) $orders->filter(fn ($o) => $o->created_at >= $from && $o->created_at < $to)->sum('total'), 2);
        }

        return [
            'labels' => $labels,
            'datasets' => [[
                'label' => 'Nilai tempahan (RM)',
                'data' => $values,
                'backgroundColor' => array_merge(array_fill(0, 7, '#0B3BA8'), ['#3B82F6']),
                'borderRadius' => 4,
                'maxBarThickness' => 48,
            ]],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => ['legend' => ['display' => false]],
            'scales' => [
                'y' => ['beginAtZero' => true, 'grid' => ['color' => 'rgba(127,127,127,.15)']],
                'x' => ['grid' => ['display' => false]],
            ],
        ];
    }
}
