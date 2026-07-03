<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'transaction_date' => 'date',
            'submitted_from_mobile' => 'boolean',
        ];
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function chartAccount()
    {
        return $this->belongsTo(ChartAccount::class);
    }

    public function assembly()
    {
        return $this->belongsTo(Assembly::class);
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
