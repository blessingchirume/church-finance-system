<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $guarded = [];

    protected static function booted(): void
    {
        static::saved(fn () => Service::recomputeBalances());
        static::deleted(fn () => Service::recomputeBalances());
    }

    protected function casts(): array
    {
        return [
            'transaction_date' => 'date',
            'submitted_from_mobile' => 'boolean',
        ];
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function chartAccount()
    {
        return $this->belongsTo(ChartAccount::class);
    }

    public function assembly()
    {
        return $this->belongsTo(Assembly::class);
    }

    public function fundingAccount()
    {
        return $this->belongsTo(ChartAccount::class, 'funding_account_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
