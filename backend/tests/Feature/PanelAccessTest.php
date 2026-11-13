<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Jobs\SendOrderEmail;
use App\Mail\OrderMail;
use App\Models\EmailLog;
use App\Models\Team;
use App\Services\OrderService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;

class PanelAccessTest extends CkTestCase
{
    public function test_guests_are_sent_to_login(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
        $this->get('/driver')->assertRedirect('/driver/login');
    }

    public function test_drivers_cannot_open_the_admin_panel(): void
    {
        $driver = $this->user(UserRole::Driver, Team::create(['name' => 'Team A']));

        $this->actingAs($driver)->get('/admin')->assertForbidden();
        $this->actingAs($driver)->get('/driver')->assertOk()->assertSee('Laluan saya');
    }

    public function test_admin_pages_load(): void
    {
        Queue::fake();
        $admin = $this->user(UserRole::Admin);
        $order = app(OrderService::class)->create($this->customer(), [$this->carpet('standard', 5, 7)], ['pickup_date' => today()]);

        $this->actingAs($admin)->get('/admin')->assertOk();
        $this->actingAs($admin)->get('/admin/orders')->assertOk()->assertSee($order->number);
        $this->actingAs($admin)->get('/admin/orders/create')->assertOk();
        $this->actingAs($admin)->get('/admin/orders/'.$order->id)->assertOk()->assertSee($order->number.'-01');
        $this->actingAs($admin)->get('/admin/orders/'.$order->id.'/edit')->assertOk();
        $this->actingAs($admin)->get('/admin/email-logs')->assertOk();
        $this->actingAs($admin)->get('/driver')->assertOk()->assertSee($order->number);
    }

    public function test_driver_route_only_shows_own_team(): void
    {
        Queue::fake();
        $a = Team::create(['name' => 'Team A']);
        $b = Team::create(['name' => 'Team B']);
        $svc = app(OrderService::class);
        $mine = $svc->create($this->customer(), [$this->carpet('standard', 5, 7)], ['pickup_date' => today(), 'team_id' => $a->id]);
        $other = $svc->create($this->customer(), [$this->carpet('standard', 5, 7)], ['pickup_date' => today(), 'team_id' => $b->id]);

        $this->actingAs($this->user(UserRole::Driver, $a))->get('/driver')
            ->assertSee($mine->number)->assertDontSee($other->number);
    }

    public function test_queued_email_is_sent_and_logged(): void
    {
        Queue::fake();
        Mail::fake();
        $order = app(OrderService::class)->create($this->customer('b@example.com'), [$this->carpet('standard', 5, 7)]);
        $log = EmailLog::sole();

        (new SendOrderEmail($log->id))->handle();

        Mail::assertSent(OrderMail::class, fn (OrderMail $m) => $m->hasTo('b@example.com') && $m->order->is($order));
        $this->assertSame('sent', $log->fresh()->status);
    }

    public function test_email_renders_items_and_total(): void
    {
        Queue::fake();
        $order = app(OrderService::class)->create($this->customer(), [$this->carpet('velvet', 4, 6)]);

        $html = (new OrderMail($order->load('items.carpetType', 'customer'), 'diterima'))->render();

        $this->assertStringContainsString($order->number.'-01', $html);
        $this->assertStringContainsString('RM63.00', $html);
    }
}
