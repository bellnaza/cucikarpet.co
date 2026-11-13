<?php

namespace App\Filament\Admin\Widgets;

use App\Filament\Admin\RangedChart;

use App\Enums\OrderStatus;

class TeamChart extends RangedChart
{
    protected static ?string $heading = 'Prestasi team (pickup + delivery selesai)';

    protected static ?int $sort = 6;

    protected function getData(): array
    {
        $done = $this->ordersInRange()->whereNotNull('team_id')->with('team:id,name')->get(['id', 'team_id', 'status'])
            ->groupBy(fn ($o) => $o->team?->name ?? '—')
            ->map(fn ($orders) => $orders->sum(fn ($o) => ($o->status->position() >= OrderStatus::Diambil->position() ? 1 : 0)
                + ($o->status === OrderStatus::Selesai ? 1 : 0)))
            ->sortDesc();

        return $this->bars($done->keys()->all(), $done->values()->all(), 'Tugasan selesai');
    }
}
