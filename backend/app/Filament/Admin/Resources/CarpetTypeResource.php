<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CarpetTypeResource\Pages;
use App\Models\CarpetType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CarpetTypeResource extends Resource
{
    protected static ?string $model = CarpetType::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationGroup = 'Tetapan';

    protected static ?string $modelLabel = 'jenis karpet';

    protected static ?string $pluralModelLabel = 'Harga karpet';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->label('Nama (BM)')->required()->maxLength(80),
            Forms\Components\TextInput::make('name_en')->label('Nama (English)')->maxLength(80),
            Forms\Components\TextInput::make('code')->label('Kod')->required()->alphaDash()->unique(ignoreRecord: true)->maxLength(30),
            Forms\Components\TextInput::make('rate_per_sqft')->label('Harga per sqft')->prefix('RM')->numeric()->minValue(0.1)->step(0.1)->required()
                ->helperText('Harga baru hanya untuk order baru atau ukuran yang dikemas kini. Order lama kekal.'),
            Forms\Components\TextInput::make('sort')->label('Susunan')->numeric()->default(0),
            Forms\Components\Toggle::make('active')->label('Aktif')->default(true),
            Forms\Components\TextInput::make('description')->label('Penerangan')->maxLength(200)->columnSpanFull(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Jenis')->weight('medium')->description(fn (CarpetType $r) => $r->description),
                Tables\Columns\TextColumn::make('rate_per_sqft')->label('Harga / sqft')->formatStateUsing(fn ($state) => 'RM'.number_format((float) $state, 2))->alignEnd(),
                Tables\Columns\IconColumn::make('active')->label('Aktif')->boolean(),
            ])
            ->reorderable('sort')
            ->defaultSort('sort')
            ->actions([Tables\Actions\EditAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCarpetTypes::route('/'),
            'create' => Pages\CreateCarpetType::route('/create'),
            'edit' => Pages\EditCarpetType::route('/{record}/edit'),
        ];
    }
}
