<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = ['name', 'phone', 'email', 'address', 'area', 'notes'];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /** Phone in wa.me format: digits only, local 0 replaced with 60. */
    public function whatsappNumber(): string
    {
        $digits = preg_replace('/\D+/', '', (string) $this->phone);

        return str_starts_with($digits, '0') ? '6'.$digits : $digits;
    }
}
