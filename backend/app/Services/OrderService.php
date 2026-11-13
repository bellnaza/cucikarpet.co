<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Jobs\SendOrderEmail;
use App\Mail\OrderMail;
use App\Models\Customer;
use App\Models\EmailLog;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderPhoto;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(private OrderNumberGenerator $numbers) {}

    /**
     * @param  Customer|array  $customer  an existing customer, or fields for a new one
     * @param  array  $items  [['carpet_type_id'=>, 'width_ft'=>, 'length_ft'=>], ...]
     * @param  array  $attributes  pickup_date, pickup_slot, team_id, notes, source
     */
    public function create(Customer|array $customer, array $items, array $attributes = [], bool $notify = true, ?User $by = null): Order
    {
        $lines = OrderPricing::lines($items);

        return DB::transaction(function () use ($customer, $lines, $attributes, $notify, $by) {
            $customer = $customer instanceof Customer ? $customer : Customer::create($customer);

            $scheduled = ! empty($attributes['pickup_date']) && ! empty($attributes['team_id']);

            $order = Order::create([
                'number' => $this->numbers->next(),
                'customer_id' => $customer->id,
                'status' => $scheduled ? OrderStatus::Pickup : OrderStatus::Baru,
                'payment_status' => PaymentStatus::Unpaid,
                'pickup_date' => $attributes['pickup_date'] ?? null,
                'pickup_slot' => $attributes['pickup_slot'] ?? null,
                'team_id' => $attributes['team_id'] ?? null,
                'notes' => $attributes['notes'] ?? null,
                'source' => $attributes['source'] ?? 'admin',
            ]);

            $this->writeItems($order, $lines);

            $order->histories()->create(['status' => OrderStatus::Baru, 'user_id' => $by?->id]);
            if ($scheduled) {
                $order->histories()->create(['status' => OrderStatus::Pickup, 'user_id' => $by?->id]);
            }

            if ($notify) {
                $this->queueEmail($order, 'diterima');
            }

            return $order->fresh(['customer', 'items']);
        });
    }

    /** Replace all carpets (e.g. after measuring at pickup). Tags are renumbered -01, -02, ... */
    public function replaceItems(Order $order, array $items, ?User $by = null): Order
    {
        $lines = OrderPricing::lines($items);

        return DB::transaction(function () use ($order, $lines, $by) {
            $this->writeItems($order, $lines);
            $order->histories()->create([
                'status' => $order->status,
                'note' => count($lines).' karpet · ukuran dikemas kini · '.OrderPricing::rm($order->total),
                'user_id' => $by?->id,
            ]);

            return $order->fresh(['items']);
        });
    }

    public function changeStatus(Order $order, OrderStatus $to, ?User $by = null, ?string $note = null, bool $notify = true): Order
    {
        if ($order->status === $to) {
            return $order;
        }

        return DB::transaction(function () use ($order, $to, $by, $note, $notify) {
            $changes = ['status' => $to];
            if ($to === OrderStatus::Hantar && ! $order->delivery_date) {
                $changes['delivery_date'] = today();
            }
            $order->update($changes);
            $order->histories()->create(['status' => $to, 'note' => $note, 'user_id' => $by?->id]);

            if ($notify && ($template = $to->emailTemplate())) {
                $this->queueEmail($order, $template);
            }

            return $order;
        });
    }

    public function markPaid(Order $order, PaymentMethod $method, ?User $by = null): Order
    {
        return DB::transaction(function () use ($order, $method, $by) {
            $order->update([
                'payment_status' => PaymentStatus::Paid,
                'payment_method' => $method,
                'paid_at' => $order->paid_at ?? now(),
            ]);
            $order->histories()->create([
                'status' => $order->status,
                'note' => 'Dibayar '.OrderPricing::rm($order->total).' · '.$method->getLabel(),
                'user_id' => $by?->id,
            ]);

            return $order;
        });
    }

    public function markUnpaid(Order $order, ?User $by = null): Order
    {
        $order->update(['payment_status' => PaymentStatus::Unpaid, 'paid_at' => null]);
        $order->histories()->create(['status' => $order->status, 'note' => 'Ditanda belum bayar', 'user_id' => $by?->id]);

        return $order;
    }

    /** Driver confirms a pickup: final measurements, optional photos, status → Diambil. */
    public function confirmPickup(Order $order, array $items, array $photoPaths = [], ?User $by = null): Order
    {
        return DB::transaction(function () use ($order, $items, $photoPaths, $by) {
            $this->replaceItems($order, $items, $by);
            foreach ($photoPaths as $path) {
                OrderPhoto::create(['order_id' => $order->id, 'path' => $path, 'kind' => 'sebelum', 'user_id' => $by?->id]);
            }

            return $this->changeStatus($order->fresh(), OrderStatus::Diambil, $by, count($photoPaths) ? count($photoPaths).' foto' : null);
        });
    }

    /** Driver completes a delivery: payment collected in full, status → Selesai, receipt email. */
    public function completeDelivery(Order $order, ?PaymentMethod $method, ?User $by = null): Order
    {
        return DB::transaction(function () use ($order, $method, $by) {
            if (! $order->isPaid()) {
                if (! $method) {
                    throw new \InvalidArgumentException('Choose how the customer paid.');
                }
                $this->markPaid($order, $method, $by);
            }

            return $this->changeStatus($order->fresh(), OrderStatus::Selesai, $by);
        });
    }

    /** Queue a customer email. Returns null when the customer has no email address. */
    public function queueEmail(Order $order, string $template): ?EmailLog
    {
        $order->loadMissing('customer');
        $to = $order->customer?->email;
        if (! $to) {
            return null;
        }

        $log = EmailLog::create([
            'order_id' => $order->id,
            'to' => $to,
            'template' => $template,
            'subject' => OrderMail::subjectFor($template, $order->number),
            'status' => 'queued',
        ]);

        SendOrderEmail::dispatch($log->id)->afterCommit();

        return $log;
    }

    private function writeItems(Order $order, array $lines): void
    {
        $order->items()->delete();

        foreach ($lines as $i => $line) {
            OrderItem::create($line + [
                'order_id' => $order->id,
                'position' => $i + 1,
                'tag' => OrderNumberGenerator::tag($order->number, $i + 1),
            ]);
        }

        $order->update(OrderPricing::totals($lines));
    }
}
