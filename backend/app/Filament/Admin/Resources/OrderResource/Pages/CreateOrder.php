<?php

namespace App\Filament\Admin\Resources\OrderResource\Pages;

use App\Filament\Admin\Resources\OrderResource;
use App\Models\Customer;
use App\Services\OrderService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return app(OrderService::class)->create(
            Customer::findOrFail($data['customer_id']),
            array_values($data['carpets'] ?? []),
            [
                'pickup_date' => $data['pickup_date'] ?? null,
                'pickup_slot' => $data['pickup_slot'] ?? null,
                'team_id' => $data['team_id'] ?? null,
                'notes' => $data['notes'] ?? null,
                'source' => 'admin',
            ],
            notify: (bool) ($data['notify_customer'] ?? false),
            by: auth()->user(),
        );
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Order '.$this->getRecord()->number.' disimpan ('.$this->getRecord()->items()->count().' karpet)';
    }
}
