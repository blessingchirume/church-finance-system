<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TuckshopSale extends Model
{
    protected $guarded = [];

    protected static function booted(): void
    {
        static::saved(fn () => Service::recomputeBalances());
        static::deleted(fn () => Service::recomputeBalances());
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
