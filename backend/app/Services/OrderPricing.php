<?php

namespace App\Services;

use App\Models\CarpetType;
use InvalidArgumentException;

/**
 * Price = width (ft) × length (ft) × the carpet type's rate per sqft.
 * Pickup & delivery is free from RM150; below that (and above 0) it costs RM15.
 */
class OrderPricing
{
    /**
     * @param  array<int, array{carpet_type_id:int|string, width_ft:float|string, length_ft:float|string}>  $items
     * @return array<int, array{carpet_type_id:int, width_ft:float, length_ft:float, sqft:float, rate:float, amount:float}>
     */
    public static function lines(array $items): array
    {
        if ($items === []) {
            throw new InvalidArgumentException('An order needs at least one carpet.');
        }

        $types = CarpetType::query()
            ->whereIn('id', array_map(fn ($i) => (int) $i['carpet_type_id'], $items))
            ->get()->keyBy('id');

        $lines = [];
        foreach (array_values($items) as $i => $item) {
            $type = $types->get((int) $item['carpet_type_id']);
            $w = round((float) $item['width_ft'], 2);
            $l = round((float) $item['length_ft'], 2);

            if (! $type) {
                throw new InvalidArgumentException('Unknown carpet type on carpet '.($i + 1).'.');
            }
            if ($w <= 0 || $l <= 0) {
                throw new InvalidArgumentException('Carpet '.($i + 1).' needs a width and length above 0.');
            }

            $sqft = round($w * $l, 2);
            $rate = (float) $type->rate_per_sqft;

            $lines[] = [
                'carpet_type_id' => $type->id,
                'width_ft' => $w,
                'length_ft' => $l,
                'sqft' => $sqft,
                'rate' => $rate,
                'amount' => round($sqft * $rate, 2),
            ];
        }

        return $lines;
    }

    public static function feeFor(float $subtotal): float
    {
        return ($subtotal > 0 && $subtotal < (float) config('cucikarpet.free_pickup_from'))
            ? (float) config('cucikarpet.pickup_fee')
            : 0.0;
    }

    /** @return array{total_sqft:float, subtotal:float, pickup_fee:float, total:float} */
    public static function totals(array $lines): array
    {
        $subtotal = round(array_sum(array_column($lines, 'amount')), 2);
        $fee = self::feeFor($subtotal);

        return [
            'total_sqft' => round(array_sum(array_column($lines, 'sqft')), 2),
            'subtotal' => $subtotal,
            'pickup_fee' => $fee,
            'total' => round($subtotal + $fee, 2),
        ];
    }

    public static function rm(float|string|null $amount): string
    {
        return 'RM'.number_format((float) $amount, 2);
    }
}
