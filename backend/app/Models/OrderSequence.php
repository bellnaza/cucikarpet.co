<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderSequence extends Model
{
    protected $primaryKey = 'period';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['period', 'last_number'];
}
