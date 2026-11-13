<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\EmailLogResource\Pages;
use App\Jobs\SendOrderEmail;
use App\Mail\OrderMail;
use App\Models\EmailLog;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EmailLogResource extends Resource
{
    protected static ?string $model = EmailLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationGroup = 'Operasi';

    protected static ?string $modelLabel = 'emel';

    protected static ?string $pluralModelLabel = 'Emel pelanggan';

    protected static ?int $navigationSort = 4;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getNavigationBadge(): ?string
    {
        $failed = EmailLog::where('status', 'failed')->count();

        return $failed ? (string) $failed : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('subject')->label('Subjek')->searchable()->weight('medium')
                    ->description(fn (EmailLog $r) => 'Kepada '.$r->to),
                Tables\Columns\TextColumn::make('template')->label('Jenis')->badge()->color('gray')
                    ->formatStateUsing(fn ($state) => OrderMail::TEMPLATES[$state] ?? $state),
                Tables\Columns\TextColumn::make('status')->label('Status')->badge()
                    ->color(fn ($state) => ['sent' => 'success', 'failed' => 'danger'][$state] ?? 'warning')
                    ->formatStateUsing(fn ($state) => ['sent' => 'Dihantar', 'failed' => 'Gagal', 'queued' => 'Dalam baris gilir'][$state] ?? $state),
                Tables\Columns\TextColumn::make('error')->label('Ralat')->limit(60)->placeholder('—')->color('danger')->toggleable(),
                Tables\Columns\TextColumn::make('created_at')->label('Dicipta')->dateTime('j M, g:i a')->sortable(),
                Tables\Columns\TextColumn::make('sent_at')->label('Dihantar')->dateTime('j M, g:i a')->placeholder('—'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(['queued' => 'Dalam baris gilir', 'sent' => 'Dihantar', 'failed' => 'Gagal']),
            ])
            ->actions([
                Tables\Actions\Action::make('resend')->label('Hantar semula')->icon('heroicon-o-arrow-path')
                    ->visible(fn (EmailLog $r) => $r->status !== 'sent')
                    ->action(function (EmailLog $r) {
                        $r->update(['status' => 'queued', 'error' => null]);
                        SendOrderEmail::dispatch($r->id);
                        Notification::make()->title('Emel dimasukkan semula ke baris gilir')->success()->send();
                    }),
                Tables\Actions\Action::make('order')->label('Order')->icon('heroicon-o-clipboard-document-list')->color('gray')
                    ->visible(fn (EmailLog $r) => $r->order_id !== null)
                    ->url(fn (EmailLog $r) => OrderResource::getUrl('view', ['record' => $r->order_id])),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListEmailLogs::route('/')];
    }
}
