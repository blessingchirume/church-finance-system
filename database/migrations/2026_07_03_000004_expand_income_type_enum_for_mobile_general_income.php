<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("
            ALTER TABLE incomes
            MODIFY type ENUM('partnership', 'offering', 'project_pledge', 'funeral', 'tuckshop', 'other') NOT NULL
        ");
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("
            ALTER TABLE incomes
            MODIFY type ENUM('partnership', 'offering', 'project_pledge', 'funeral', 'tuckshop') NOT NULL
        ");
    }
};
