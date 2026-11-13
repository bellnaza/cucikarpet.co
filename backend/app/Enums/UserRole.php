<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum UserRole: string implements HasLabel
{
    case Admin = 'admin';
    case Driver = 'driver';

    public function getLabel(): string
    {
        return $this === self::Admin ? 'Admin' : 'Team pickup/delivery';
    }
}
