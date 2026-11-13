<?php

namespace App\Filament\Admin;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Builder;

/** Shared look and the "Bulan ini / 90 hari / Semua" filter for the horizontal bar charts. */
abstract class RangedChart extends ChartWidget
{
    public ?string $filter = 'month';

    protected static ?string $maxHeight = '260px';

    protected const BRAND = '#0B3BA8';

    protected function getFilters(): ?array
    {
        return ['month' => 'Bulan ini', '90' => '90 hari', 'all' => 'Semua'];
    }

    protected function ordersInRange(): Builder
    {
        $q = Order::query()->notCancelled();

        return match ($this->filter) {
            'month' => $q->where('created_at', '>=', today()->startOfMonth()),
            '90' => $q->where('created_at', '>=', today()->subDays(90)),
            default => $q,
        };
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y',
            'plugins' => ['legend' => ['display' => false]],
            'scales' => [
                'x' => ['beginAtZero' => true, 'ticks' => ['precision' => 0], 'grid' => ['color' => 'rgba(127,127,127,.15)']],
                'y' => ['grid' => ['display' => false]],
            ],
        ];
    }

    protected function bars(array $labels, array $values, string $label): array
    {
        return [
            'labels' => $labels,
            'datasets' => [[
                'label' => $label,
                'data' => $values,
                'backgroundColor' => self::BRAND,
                'borderRadius' => 4,
                'borderSkipped' => 'start',
                'maxBarThickness' => 22,
            ]],
        ];
    }
}
