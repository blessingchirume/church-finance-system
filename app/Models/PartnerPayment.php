<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnerPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'partner_id',
        'amount_paid',
        'payment_date',
        'payment_method',
        'reference_number',
        'month_year'
    ];

    protected $casts = [
        'payment_date' => 'date',
        'month_year' => 'date'
    ];

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }
}