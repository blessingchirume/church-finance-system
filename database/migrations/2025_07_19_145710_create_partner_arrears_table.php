<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('partner_arrears', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_id')->constrained()->onDelete('cascade');
            $table->date('month_year'); // Stored as YYYY-MM-01
            $table->decimal('expected_amount', 10, 2);
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->decimal('balance', 10, 2);
            $table->boolean('is_settled')->default(false);
            $table->timestamps();
            
            $table->unique(['partner_id', 'month_year']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('partner_arrears');
    }
};
