<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('incomes', function (Blueprint $table) {
            $table->date('transaction_date')->nullable()->after('type');
            $table->string('purpose')->nullable()->after('pledge_campaign');
            $table->string('currency', 3)->default('USD')->after('amount');
            $table->string('payment_method')->nullable()->after('currency');
            $table->string('mobile_client_id')->nullable()->unique()->after('payment_method');
            $table->boolean('submitted_from_mobile')->default(false)->after('mobile_client_id');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->date('transaction_date')->nullable()->after('service_id');
            $table->string('purpose')->nullable()->after('category');
            $table->string('currency', 3)->default('USD')->after('amount');
            $table->string('payment_method')->nullable()->after('currency');
            $table->string('mobile_client_id')->nullable()->unique()->after('payment_method');
            $table->boolean('submitted_from_mobile')->default(false)->after('mobile_client_id');
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn([
                'transaction_date',
                'purpose',
                'currency',
                'payment_method',
                'mobile_client_id',
                'submitted_from_mobile',
            ]);
        });

        Schema::table('incomes', function (Blueprint $table) {
            $table->dropColumn([
                'transaction_date',
                'purpose',
                'currency',
                'payment_method',
                'mobile_client_id',
                'submitted_from_mobile',
            ]);
        });
    }
};
