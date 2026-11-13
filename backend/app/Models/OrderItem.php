<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = ['order_id', 'position', 'tag', 'carpet_type_id', 'width_ft', 'length_ft', 'sqft', 'rate', 'amount'];

    protected function casts(): array
    {
        return [
            'width_ft' => 'decimal:2',
            'length_ft' => 'decimal:2',
            'sqft' => 'decimal:2',
            'rate' => 'decimal:2',
            'amount' => 'decimal:2',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function carpetType(): BelongsTo
    {
        return $this->belongsTo(CarpetType::class);
    }
}
