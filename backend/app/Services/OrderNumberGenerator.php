<?php

namespace App\Services;

use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

/**
 * Hands out order numbers like CK-2611-0007: prefix, YYMM, then a sequence
 * that restarts at 0001 every month. The month's row is locked while the next
 * number is taken, so two people saving at the same moment never get the same one.
 */
class OrderNumberGenerator
{
    public function next(?CarbonInterface $at = null): string
    {
        $period = ($at ?? now())->format('ym');

        $number = DB::transaction(function () use ($period) {
            DB::table('order_sequences')->insertOrIgnore([
                'period' => $period,
                'last_number' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $row = DB::table('order_sequences')->where('period', $period)->lockForUpdate()->first();
            $next = ((int) $row->last_number) + 1;

            DB::table('order_sequences')->where('period', $period)->update([
                'last_number' => $next,
                'updated_at' => now(),
            ]);

            return $next;
        });

        return sprintf('%s-%s-%04d', config('cucikarpet.order_prefix', 'CK'), $period, $number);
    }

    /** Tag for the n-th carpet (1-based) of an order: CK-2611-0007-01. */
    public static function tag(string $orderNumber, int $position): string
    {
        return sprintf('%s-%02d', $orderNumber, $position);
    }
}
