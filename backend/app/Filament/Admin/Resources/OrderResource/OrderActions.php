<?php

namespace App\Filament\Admin\Resources\OrderResource;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Models\Order;
use App\Services\OrderPricing;
use App\Services\OrderService;
use Filament\Actions\Action as PageAction;
use Filament\Forms;
use Filament\Notifications\Notification;

/**
 * Order actions shared by the table (Filament\Tables\Actions\Action) and the
 * view page (Filament\Actions\Action). Pass the action class to build.
 */
class OrderActions
{
    public static function next(string $class = PageAction::class)
    {
        return $class::make('nextStatus')
            ->label(fn (Order $record) => 'Tukar ke: '.$record->status->next()?->getLabel())
            ->icon('heroicon-o-arrow-right-circle')
            ->color('primary')
            ->visible(fn (Order $record) => $record->status !== OrderStatus::Batal && $record->status->next() !== null)
            ->modalHeading(fn (Order $record) => $record->number.' → '.$record->status->next()?->getLabel())
            ->modalSubmitActionLabel('Tukar status')
            ->form(fn (Order $record) => ($record->status->next()?->emailTemplate() && $record->customer?->email)
                ? [Forms\Components\Toggle::make('notify')->label('Emel pelanggan ('.$record->customer->email.')')->default(true)]
                : [Forms\Components\Placeholder::make('info')->label('')->content('Tiada emel dihantar untuk langkah ini.')])
            ->action(function (Order $record, array $data) {
                $to = $record->status->next();
                app(OrderService::class)->changeStatus($record, $to, auth()->user(), null, (bool) ($data['notify'] ?? false));
                Notification::make()->title($record->number.': '.$to->getLabel())->success()->send();
            });
    }

    public static function setStatus(string $class = PageAction::class)
    {
        return $class::make('setStatus')
            ->label('Tetapkan status')
            ->icon('heroicon-o-adjustments-horizontal')
            ->color('gray')
            ->fillForm(fn (Order $record) => ['status' => $record->status->value, 'notify' => false])
            ->form([
                Forms\Components\Select::make('status')->label('Status')->options(OrderStatus::options())->required(),
                Forms\Components\TextInput::make('note')->label('Nota (pilihan)')->maxLength(200),
                Forms\Components\Toggle::make('notify')->label('Emel pelanggan jika ada templat untuk status ini'),
            ])
            ->action(function (Order $record, array $data) {
                $to = OrderStatus::from($data['status']);
                app(OrderService::class)->changeStatus($record, $to, auth()->user(), $data['note'] ?? null, (bool) $data['notify']);
                Notification::make()->title($record->number.': '.$to->getLabel())->success()->send();
            });
    }

    public static function markPaid(string $class = PageAction::class)
    {
        return $class::make('markPaid')
            ->label(fn (Order $record) => 'Tanda dibayar '.OrderPricing::rm($record->total))
            ->icon('heroicon-o-banknotes')
            ->color('success')
            ->visible(fn (Order $record) => ! $record->isPaid() && $record->status !== OrderStatus::Batal)
            ->form([
                Forms\Components\Select::make('payment_method')->label('Kaedah bayaran')->options(PaymentMethod::class)->required(),
            ])
            ->action(function (Order $record, array $data) {
                $method = $data['payment_method'] instanceof PaymentMethod ? $data['payment_method'] : PaymentMethod::from($data['payment_method']);
                app(OrderService::class)->markPaid($record, $method, auth()->user());
                Notification::make()->title($record->number.' ditanda dibayar')->success()->send();
            });
    }

    public static function markUnpaid(string $class = PageAction::class)
    {
        return $class::make('markUnpaid')
            ->label('Tanda belum bayar')
            ->icon('heroicon-o-arrow-uturn-left')
            ->color('gray')
            ->visible(fn (Order $record) => $record->isPaid())
            ->requiresConfirmation()
            ->action(fn (Order $record) => app(OrderService::class)->markUnpaid($record, auth()->user()));
    }

    public static function whatsapp(string $class = PageAction::class)
    {
        return $class::make('whatsapp')
            ->label('WhatsApp')
            ->icon('heroicon-o-chat-bubble-left-right')
            ->color('gray')
            ->url(fn (Order $record) => 'https://wa.me/'.$record->customer->whatsappNumber())
            ->openUrlInNewTab();
    }
}
