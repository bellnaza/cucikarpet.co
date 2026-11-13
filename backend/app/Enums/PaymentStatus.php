<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum PaymentStatus: string implements HasColor, HasLabel
{
    case Unpaid = 'unpaid';
    case Paid = 'paid';

    public function getLabel(): string
    {
        return $this === self::Paid ? 'Dibayar' : 'Belum bayar';
    }

    public function getColor(): string
    {
        return $this === self::Paid ? 'success' : 'warning';
    }
}
