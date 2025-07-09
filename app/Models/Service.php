<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'service_date',
        'description',
        'opening_balance',
        'closing_balance',
    ];

    public function incomes()
    {
        return $this->hasMany(Income::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function tuckshopSales()
    {
        return $this->hasMany(TuckshopSale::class);
    }
}
