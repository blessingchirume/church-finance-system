<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnerArrear extends Model
{
    use HasFactory;

    protected $fillable = [
        'partner_id',
        'month_year',
        'expected_amount',
        'amount_paid',
        'balance',
        'is_settled'
    ];

    protected $casts = [
        'month_year' => 'date',
        'is_settled' => 'boolean'
    ];

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }
}