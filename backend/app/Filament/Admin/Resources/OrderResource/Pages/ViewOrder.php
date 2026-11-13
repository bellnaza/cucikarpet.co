<?php

namespace App\Filament\Admin\Resources\OrderResource\Pages;

use App\Filament\Admin\Resources\OrderResource;
use App\Filament\Admin\Resources\OrderResource\OrderActions;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    public function getTitle(): string
    {
        return $this->getRecord()->number;
    }

    public function getSubheading(): ?string
    {
        $o = $this->getRecord();

        return $o->customer->name.' · '.$o->items()->count().' karpet · dicipta '.$o->created_at->format('j M Y, g:i a');
    }

    protected function getHeaderActions(): array
    {
        return [
            OrderActions::next(),
            OrderActions::markPaid(),
            Actions\ActionGroup::make([
                OrderActions::setStatus(),
                OrderActions::markUnpaid(),
                OrderActions::whatsapp(),
                Actions\EditAction::make()->label('Edit order & ukuran'),
            ])->button()->label('Lagi')->color('gray'),
        ];
    }
}
