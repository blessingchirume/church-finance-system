<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $guarded = [];

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function chartAccount()
    {
        return $this->belongsTo(ChartAccount::class);
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
