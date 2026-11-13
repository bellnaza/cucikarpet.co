<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum OrderStatus: string implements HasColor, HasLabel
{
    case Baru = 'baru';
    case Disahkan = 'disahkan';
    case Pickup = 'pickup';
    case Diambil = 'diambil';
    case Cucian = 'cucian';
    case Kering = 'kering';
    case Qc = 'qc';
    case Sedia = 'sedia';
    case Hantar = 'hantar';
    case Selesai = 'selesai';
    case Batal = 'batal';

    public function getLabel(): string
    {
        return match ($this) {
            self::Baru => 'Tempahan baru',
            self::Disahkan => 'Disahkan',
            self::Pickup => 'Pickup dijadual',
            self::Diambil => 'Diambil & diukur',
            self::Cucian => 'Dalam cucian',
            self::Kering => 'Pengeringan',
            self::Qc => 'Pemeriksaan (QC)',
            self::Sedia => 'Sedia dihantar',
            self::Hantar => 'Dalam penghantaran',
            self::Selesai => 'Selesai',
            self::Batal => 'Dibatalkan',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Baru, self::Disahkan => 'gray',
            self::Pickup, self::Diambil, self::Hantar => 'info',
            self::Cucian, self::Kering, self::Qc, self::Sedia => 'warning',
            self::Selesai => 'success',
            self::Batal => 'danger',
        };
    }

    /** The normal flow, in order (excludes Batal). */
    public static function flow(): array
    {
        return [
            self::Baru, self::Disahkan, self::Pickup, self::Diambil, self::Cucian,
            self::Kering, self::Qc, self::Sedia, self::Hantar, self::Selesai,
        ];
    }

    public function position(): int
    {
        $i = array_search($this, self::flow(), true);

        return $i === false ? -1 : $i;
    }

    public function next(): ?self
    {
        $flow = self::flow();
        $i = $this->position();

        return ($i >= 0 && $i < count($flow) - 1) ? $flow[$i + 1] : null;
    }

    public function isActive(): bool
    {
        return ! in_array($this, [self::Selesai, self::Batal], true);
    }

    public function isInWorkshop(): bool
    {
        return in_array($this, [self::Cucian, self::Kering, self::Qc, self::Sedia], true);
    }

    /** Email template sent to the customer when an order enters this status. */
    public function emailTemplate(): ?string
    {
        return match ($this) {
            self::Pickup => 'pickup',
            self::Hantar => 'hantar',
            self::Selesai => 'selesai',
            default => null,
        };
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        $out = [];
        foreach (self::cases() as $case) {
            $out[$case->value] = $case->getLabel();
        }

        return $out;
    }
}
