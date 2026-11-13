<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Jobs\SendOrderEmail;
use App\Models\EmailLog;
use App\Models\Team;
use App\Services\OrderService;
use Illuminate\Support\Facades\Queue;
use InvalidArgumentException;

class OrderServiceTest extends CkTestCase
{
    private OrderService $svc;

    protected function setUp(): void
    {
        parent::setUp();
        Queue::fake();
        $this->svc = app(OrderService::class);
    }

    public function test_each_carpet_gets_a_numbered_tag_and_prices_add_up(): void
    {
        $order = $this->svc->create($this->customer(), [
            $this->carpet('wool', 5, 8),       // 40 sqft × 5.00 = 200.00
            $this->carpet('standard', 6, 9),   // 54 sqft × 3.20 = 172.80
            $this->carpet('standard', 4, 6),   // 24 sqft × 3.20 =  76.80
        ]);

        $this->assertMatchesRegularExpression('/^CK-\d{4}-0001$/', $order->number);
        $this->assertSame(
            [$order->number.'-01', $order->number.'-02', $order->number.'-03'],
            $order->items->pluck('tag')->all()
        );
        $this->assertEquals(118, (float) $order->total_sqft);
        $this->assertEquals(449.60, (float) $order->subtotal);
        $this->assertEquals(0, (float) $order->pickup_fee);
        $this->assertEquals(449.60, (float) $order->total);
    }

    public function test_pickup_fee_applies_below_rm150_only(): void
    {
        $small = $this->svc->create($this->customer(), [$this->carpet('velvet', 4, 6)]); // RM48
        $this->assertEquals(15, (float) $small->pickup_fee);
        $this->assertEquals(63, (float) $small->total);

        $exact = $this->svc->create($this->customer(), [$this->carpet('wool', 5, 6)]); // RM150 exactly
        $this->assertEquals(0, (float) $exact->pickup_fee);
        $this->assertEquals(150, (float) $exact->total);
    }

    public function test_status_starts_as_pickup_when_date_and_team_given(): void
    {
        $team = Team::create(['name' => 'Team A']);
        $scheduled = $this->svc->create($this->customer(), [$this->carpet('shaggy', 5, 7)], ['pickup_date' => today(), 'team_id' => $team->id]);
        $unscheduled = $this->svc->create($this->customer(), [$this->carpet('shaggy', 5, 7)], ['pickup_date' => today()]);

        $this->assertSame(OrderStatus::Pickup, $scheduled->status);
        $this->assertSame(OrderStatus::Baru, $unscheduled->status);
        $this->assertSame(2, $scheduled->histories()->count());
    }

    public function test_emails_are_queued_only_for_customers_with_email(): void
    {
        $this->svc->create($this->customer('a@example.com'), [$this->carpet('standard', 5, 7)]);
        $this->svc->create($this->customer(null), [$this->carpet('standard', 5, 7)]);

        $this->assertSame(1, EmailLog::count());
        $this->assertSame('diterima', EmailLog::first()->template);
        Queue::assertPushed(SendOrderEmail::class, 1);
    }

    public function test_status_changes_record_history_and_queue_the_right_emails(): void
    {
        $order = $this->svc->create($this->customer(), [$this->carpet('standard', 5, 7)], notify: false);

        $this->svc->changeStatus($order, OrderStatus::Diambil);   // no email
        $this->svc->changeStatus($order, OrderStatus::Hantar);    // "dalam perjalanan" email

        $order->refresh();
        $this->assertSame(OrderStatus::Hantar, $order->status);
        $this->assertTrue($order->delivery_date->isToday());
        $this->assertSame(['hantar'], EmailLog::pluck('template')->all());
        $this->assertSame(3, $order->histories()->count());
    }

    public function test_remeasuring_renumbers_tags_and_recomputes_totals(): void
    {
        $order = $this->svc->create($this->customer(), [$this->carpet('velvet', 4, 6)], notify: false);
        $this->assertEquals(63, (float) $order->total);

        $order = $this->svc->replaceItems($order, [$this->carpet('velvet', 4, 6), $this->carpet('shaggy', 6, 9)]);

        $this->assertSame([$order->number.'-01', $order->number.'-02'], $order->items->pluck('tag')->all());
        $this->assertEquals(237, (float) $order->subtotal); // 48 + 189
        $this->assertEquals(0, (float) $order->pickup_fee);
    }

    public function test_complete_delivery_requires_payment_method_then_marks_paid(): void
    {
        $order = $this->svc->create($this->customer(), [$this->carpet('standard', 8, 10)], notify: false);
        $this->svc->changeStatus($order, OrderStatus::Hantar, notify: false);

        try {
            $this->svc->completeDelivery($order->fresh(), null);
            $this->fail('Expected a payment method to be required.');
        } catch (InvalidArgumentException) {
        }

        $order = $this->svc->completeDelivery($order->fresh(), PaymentMethod::DuitNow);

        $this->assertSame(OrderStatus::Selesai, $order->status);
        $this->assertSame(PaymentStatus::Paid, $order->fresh()->payment_status);
        $this->assertSame(PaymentMethod::DuitNow, $order->fresh()->payment_method);
        $this->assertSame('selesai', EmailLog::latest('id')->first()->template);
    }

    public function test_confirm_pickup_saves_measurements_photos_and_status(): void
    {
        $order = $this->svc->create($this->customer(), [$this->carpet('standard', 5, 7)], notify: false);

        $order = $this->svc->confirmPickup($order, [$this->carpet('standard', 5, 8)], ['order-photos/a.jpg']);

        $this->assertSame(OrderStatus::Diambil, $order->status);
        $this->assertEquals(128, (float) $order->fresh()->subtotal);
        $this->assertSame(1, $order->photos()->count());
    }
}
