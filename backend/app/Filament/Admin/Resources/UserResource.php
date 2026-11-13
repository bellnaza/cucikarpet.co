<?php

namespace App\Filament\Admin\Resources;

use App\Enums\UserRole;
use App\Filament\Admin\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Validation\Rules\Password;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';

    protected static ?string $navigationGroup = 'Tetapan';

    protected static ?string $modelLabel = 'pengguna';

    protected static ?string $pluralModelLabel = 'Pengguna & driver';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->label('Nama')->required()->maxLength(120),
            Forms\Components\TextInput::make('email')->label('Emel (untuk log masuk)')->email()->required()->unique(ignoreRecord: true),
            Forms\Components\TextInput::make('phone')->label('Telefon')->tel()->maxLength(30),
            Forms\Components\Select::make('role')->label('Peranan')->options(UserRole::class)->required()->default(UserRole::Driver)->live()
                ->helperText('Admin: semua menu di /admin. Team: hanya app delivery di /driver.'),
            Forms\Components\Select::make('team_id')->label('Team')->relationship('team', 'name')
                ->required(fn (Get $get) => $get('role') === UserRole::Driver->value || $get('role') === UserRole::Driver),
            Forms\Components\TextInput::make('password')->label('Kata laluan')->password()->revealable()
                ->rule(Password::min(10))
                ->required(fn (string $operation) => $operation === 'create')
                ->dehydrated(fn (?string $state) => filled($state))
                ->helperText(fn (string $operation) => $operation === 'edit' ? 'Kosongkan untuk kekalkan kata laluan sekarang.' : null),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nama')->searchable()->weight('medium'),
                Tables\Columns\TextColumn::make('email')->label('Emel')->searchable(),
                Tables\Columns\TextColumn::make('role')->label('Peranan')->badge(),
                Tables\Columns\TextColumn::make('team.name')->label('Team')->placeholder('—'),
            ])
            ->actions([Tables\Actions\EditAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
