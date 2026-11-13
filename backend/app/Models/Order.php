<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'number', 'customer_id', 'status', 'payment_status', 'payment_method', 'paid_at',
        'pickup_date', 'pickup_slot', 'pickup_seq', 'delivery_date', 'delivery_seq', 'team_id',
        'total_sqft', 'subtotal', 'pickup_fee', 'total', 'notes', 'source',
    ];

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'payment_status' => PaymentStatus::class,
            'payment_method' => PaymentMethod::class,
            'paid_at' => 'datetime',
            'pickup_date' => 'date',
            'delivery_date' => 'date',
            'total_sqft' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'pickup_fee' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class)->orderBy('position');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(OrderPhoto::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class)->latest('id');
    }

    public function emailLogs(): HasMany
    {
        return $this->hasMany(EmailLog::class)->latest('id');
    }

    public function scopeActive(Builder $q): Builder
    {
        return $q->whereNotIn('status', [OrderStatus::Selesai->value, OrderStatus::Batal->value]);
    }

    public function scopeNotCancelled(Builder $q): Builder
    {
        return $q->where('status', '!=', OrderStatus::Batal->value);
    }

    public function isPaid(): bool
    {
        return $this->payment_status === PaymentStatus::Paid;
    }
}
