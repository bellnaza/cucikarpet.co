<?php

namespace App\Filament\Admin\Resources\OrderResource\Pages;

use App\Filament\Admin\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    public function getTitle(): string
    {
        return 'Edit '.$this->getRecord()->number;
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['carpets'] = $this->getRecord()->items->map(fn ($i) => [
            'carpet_type_id' => $i->carpet_type_id,
            'width_ft' => (float) $i->width_ft,
            'length_ft' => (float) $i->length_ft,
        ])->all();

        return $data;
    }

    /** @param  Order  $record */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return DB::transaction(function () use ($record, $data) {
            $record->update([
                'customer_id' => $data['customer_id'],
                'pickup_date' => $data['pickup_date'] ?? null,
                'pickup_slot' => $data['pickup_slot'] ?? null,
                'team_id' => $data['team_id'] ?? null,
                'delivery_date' => $data['delivery_date'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            $new = array_values(array_map(fn ($c) => [
                'carpet_type_id' => (int) $c['carpet_type_id'],
                'width_ft' => round((float) $c['width_ft'], 2),
                'length_ft' => round((float) $c['length_ft'], 2),
            ], $data['carpets'] ?? []));
            $old = $record->items->map(fn ($i) => [
                'carpet_type_id' => (int) $i->carpet_type_id,
                'width_ft' => round((float) $i->width_ft, 2),
                'length_ft' => round((float) $i->length_ft, 2),
            ])->all();

            if ($new !== $old) {
                app(OrderService::class)->replaceItems($record, $new, auth()->user());
            }

            return $record->refresh();
        });
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()->modalDescription('Order, karpet, foto dan sejarahnya akan dipadam terus.'),
        ];
    }
}
