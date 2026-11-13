<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PaymentMethod: string implements HasLabel
{
    case Tunai = 'tunai';
    case DuitNow = 'duitnow';
    case Bank = 'bank';

    public function getLabel(): string
    {
        return match ($this) {
            self::Tunai => 'Tunai',
            self::DuitNow => 'DuitNow QR',
            self::Bank => 'Pindahan bank',
        };
    }
}
