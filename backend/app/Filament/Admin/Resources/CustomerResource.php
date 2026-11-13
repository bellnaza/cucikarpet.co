<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CustomerResource\Pages;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Operasi';

    protected static ?string $modelLabel = 'pelanggan';

    protected static ?string $pluralModelLabel = 'Pelanggan';

    protected static ?int $navigationSort = 2;

    /** Shared with the "new customer" popup on the order form. */
    public static function formSchema(): array
    {
        return [
            Forms\Components\TextInput::make('name')->label('Nama')->required()->maxLength(120),
            Forms\Components\TextInput::make('phone')->label('Telefon (WhatsApp)')->tel()->required()->maxLength(30)->placeholder('012-345 6789'),
            Forms\Components\TextInput::make('email')->label('Emel')->email()->maxLength(150)->helperText('Untuk notifikasi order. Boleh dikosongkan.'),
            Forms\Components\Select::make('area')->label('Kawasan')->options(array_combine(config('cucikarpet.areas'), config('cucikarpet.areas'))),
            Forms\Components\Textarea::make('address')->label('Alamat pickup')->required()->rows(3)->columnSpanFull(),
            Forms\Components\Textarea::make('notes')->label('Nota')->rows(2)->columnSpanFull()->placeholder('Contoh: pagar biru, call dulu'),
        ];
    }

    public static function form(Form $form): Form
    {
        return $form->schema(self::formSchema())->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nama')->searchable()->sortable()->weight('medium'),
                Tables\Columns\TextColumn::make('phone')->label('Telefon')->searchable()->copyable(),
                Tables\Columns\TextColumn::make('email')->label('Emel')->searchable()->placeholder('—')->toggleable(),
                Tables\Columns\TextColumn::make('area')->label('Kawasan')->badge()->color('gray'),
                Tables\Columns\TextColumn::make('orders_count')->counts('orders')->label('Order')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label('Daftar')->date('d M Y')->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('area')->label('Kawasan')->options(array_combine(config('cucikarpet.areas'), config('cucikarpet.areas'))),
            ])
            ->actions([
                Tables\Actions\Action::make('whatsapp')->label('WhatsApp')->icon('heroicon-o-chat-bubble-left-right')
                    ->url(fn (Customer $record) => 'https://wa.me/'.$record->whatsappNumber())->openUrlInNewTab(),
                Tables\Actions\EditAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}
