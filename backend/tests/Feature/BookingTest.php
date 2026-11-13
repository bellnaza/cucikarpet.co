<?php

namespace Tests\Feature;

use App\Models\Order;
use Illuminate\Support\Facades\Queue;

class BookingTest extends CkTestCase
{
    private function payload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Aminah',
            'phone' => '012-345 6789',
            'email' => 'aminah@example.com',
            'address' => '12, Jalan SS2/24, Petaling Jaya',
            'area' => 'Selangor',
            'pickup_date' => today()->addDay()->toDateString(),
            'pickup_slot' => 'Pagi (9–12)',
            'items_json' => json_encode([
                ['type' => 'standard', 'w' => 5, 'l' => 7, 'q' => 2],
                ['type' => 'shaggy', 'w' => 6, 'l' => 9, 'q' => 1],
            ]),
        ], $overrides);
    }

    public function test_home_page_renders_with_prices(): void
    {
        $this->get('/')->assertOk()->assertSee('cucikarpet.co')->assertSee('"standard":3.2', false);
    }

    public function test_booking_creates_a_website_order_with_one_tag_per_carpet(): void
    {
        Queue::fake();

        $this->post('/tempah', $this->payload())->assertRedirect()->assertSessionHas('booking');

        $order = Order::with('items', 'customer')->sole();
        $this->assertSame('website', $order->source);
        $this->assertCount(3, $order->items); // quantity 2 + 1
        $this->assertSame($order->number.'-03', $order->items->last()->tag);
        $this->assertEquals(413, (float) $order->total); // 112 + 112 + 189
        $this->assertSame('aminah@example.com', $order->customer->email);
    }

    public function test_booking_without_carpets_is_rejected(): void
    {
        $this->post('/tempah', $this->payload(['items_json' => '[]']))->assertSessionHasErrors('items');
        $this->assertSame(0, Order::count());
    }

    public function test_unknown_carpet_type_and_past_dates_are_rejected(): void
    {
        $this->post('/tempah', $this->payload([
            'items_json' => json_encode([['type' => 'emas', 'w' => 5, 'l' => 7]]),
            'pickup_date' => today()->subDay()->toDateString(),
        ]))->assertSessionHasErrors(['items.0.type', 'pickup_date']);
    }

    public function test_honeypot_submissions_are_silently_dropped(): void
    {
        $this->post('/tempah', $this->payload(['website' => 'http://spam.example']))->assertRedirect();
        $this->assertSame(0, Order::count());
    }
}
