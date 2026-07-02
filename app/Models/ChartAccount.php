<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChartAccount extends Model
{
    public const TYPES = ['asset', 'liability', 'income', 'expense', 'equity'];

    protected $guarded = [];

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function incomes()
    {
        return $this->hasMany(Income::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function getDisplayNameAttribute(): string
    {
        return "{$this->code} - {$this->name}";
    }
}
