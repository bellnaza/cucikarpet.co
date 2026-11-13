<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarpetType extends Model
{
    protected $fillable = ['code', 'name', 'name_en', 'rate_per_sqft', 'description', 'sort', 'active'];

    protected function casts(): array
    {
        return [
            'rate_per_sqft' => 'decimal:2',
            'active' => 'boolean',
        ];
    }

    /** @return array<int, string> id => "Name · RM3.20/sqft" for selects. */
    public static function selectOptions(): array
    {
        return static::query()->where('active', true)->orderBy('sort')->get()
            ->mapWithKeys(fn (self $t) => [$t->id => $t->name.' · RM'.number_format((float) $t->rate_per_sqft, 2).'/sqft'])
            ->all();
    }
}
