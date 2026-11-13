<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\TeamResource\Pages;
use App\Models\Team;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TeamResource extends Resource
{
    protected static ?string $model = Team::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationGroup = 'Tetapan';

    protected static ?string $modelLabel = 'team';

    protected static ?string $pluralModelLabel = 'Team';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->label('Nama team')->required()->unique(ignoreRecord: true)->maxLength(60),
            Forms\Components\TextInput::make('vehicle')->label('Kenderaan')->placeholder('Contoh: Van VKL 1234')->maxLength(60),
            Forms\Components\Toggle::make('active')->label('Aktif')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Team')->weight('medium'),
                Tables\Columns\TextColumn::make('vehicle')->label('Kenderaan')->placeholder('—'),
                Tables\Columns\TextColumn::make('members_count')->counts('members')->label('Ahli'),
                Tables\Columns\TextColumn::make('orders_count')->counts('orders')->label('Order'),
                Tables\Columns\IconColumn::make('active')->label('Aktif')->boolean(),
            ])
            ->actions([Tables\Actions\EditAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTeams::route('/'),
            'create' => Pages\CreateTeam::route('/create'),
            'edit' => Pages\EditTeam::route('/{record}/edit'),
        ];
    }
}
