<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('partner_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_id')->constrained()->onDelete('cascade');
            $table->decimal('amount_paid', 10, 2);
            $table->date('payment_date');
            $table->string('payment_method', 50);
            $table->string('reference_number', 100)->nullable();
            $table->date('month_year'); // Stored as YYYY-MM-01
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('partner_payments');
    }
};