<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $runningBalance = 0.0;

        DB::table('services')
            ->orderBy('service_date')
            ->orderBy('id')
            ->select(['id'])
            ->chunk(100, function ($services) use (&$runningBalance) {
                foreach ($services as $service) {
                    $incomeTotal = (float) DB::table('incomes')
                        ->where('service_id', $service->id)
                        ->sum('amount');
                    $tuckshopTotal = (float) DB::table('tuckshop_sales')
                        ->where('service_id', $service->id)
                        ->sum('amount');
                    $expenseTotal = (float) DB::table('expenses')
                        ->where('service_id', $service->id)
                        ->sum('amount');
                    $closingBalance = $runningBalance + $incomeTotal + $tuckshopTotal - $expenseTotal;

                    DB::table('services')
                        ->where('id', $service->id)
                        ->update([
                            'opening_balance' => $runningBalance,
                            'closing_balance' => $closingBalance,
                            'updated_at' => now(),
                        ]);

                    $runningBalance = $closingBalance;
                }
            });
    }

    public function down(): void
    {
        //
    }
};
