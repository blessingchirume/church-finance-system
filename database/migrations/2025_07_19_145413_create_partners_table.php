<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('partners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->onDelete('cascade');
            $table->decimal('commitment_amount', 10, 2);
            $table->date('commitment_start_date');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->unique(['member_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('partners');
    }
};

