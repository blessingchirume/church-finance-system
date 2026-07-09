<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'service_date',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'service_date' => 'date',
            'opening_balance' => 'decimal:2',
            'closing_balance' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::saved(fn () => self::recomputeBalances());
        static::deleted(fn () => self::recomputeBalances());
    }

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

    public function incomeTotal(): float
    {
        return (float) $this->incomes()->sum('amount');
    }

    public function tuckshopTotal(): float
    {
        return (float) $this->tuckshopSales()->sum('amount');
    }

    public function expenseTotal(): float
    {
        return (float) $this->expenses()->sum('amount');
    }

    public function totalInflow(): float
    {
        return $this->incomeTotal() + $this->tuckshopTotal();
    }

    public function netMovement(): float
    {
        return $this->totalInflow() - $this->expenseTotal();
    }

    public static function recomputeBalances(): void
    {
        $runningBalance = 0.0;

        self::query()
            ->orderBy('service_date')
            ->orderBy('id')
            ->get()
            ->each(function (Service $service) use (&$runningBalance) {
                $closingBalance = $runningBalance + $service->netMovement();

                $service->forceFill([
                    'opening_balance' => $runningBalance,
                    'closing_balance' => $closingBalance,
                ])->saveQuietly();

                $runningBalance = $closingBalance;
            });
    }
}
