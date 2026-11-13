<?php

namespace Tests\Feature;

use App\Services\OrderNumberGenerator;
use Illuminate\Support\Carbon;

class OrderNumberTest extends CkTestCase
{
    public function test_numbers_are_sequential_within_a_month(): void
    {
        $gen = app(OrderNumberGenerator::class);
        $nov = Carbon::create(2026, 11, 14);

        $this->assertSame('CK-2611-0001', $gen->next($nov));
        $this->assertSame('CK-2611-0002', $gen->next($nov));
        $this->assertSame('CK-2611-0003', $gen->next($nov->copy()->endOfMonth()));
    }

    public function test_sequence_restarts_each_month(): void
    {
        $gen = app(OrderNumberGenerator::class);

        $gen->next(Carbon::create(2026, 11, 30));
        $gen->next(Carbon::create(2026, 11, 30));

        $this->assertSame('CK-2612-0001', $gen->next(Carbon::create(2026, 12, 1)));
        $this->assertSame('CK-2611-0003', $gen->next(Carbon::create(2026, 11, 30)));
    }

    public function test_carpet_tags_use_two_digit_suffix(): void
    {
        $this->assertSame('CK-2611-0007-01', OrderNumberGenerator::tag('CK-2611-0007', 1));
        $this->assertSame('CK-2611-0007-12', OrderNumberGenerator::tag('CK-2611-0007', 12));
    }
}
