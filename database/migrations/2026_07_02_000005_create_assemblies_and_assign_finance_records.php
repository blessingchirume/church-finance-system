<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assemblies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 30)->unique();
            $table->string('location')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });

        Schema::create('assembly_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assembly_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['assembly_id', 'user_id']);
        });

        $defaultAssemblyId = DB::table('assemblies')->insertGetId([
            'name' => 'Foundation of Hope Main Assembly',
            'code' => 'MAIN',
            'location' => 'Default assembly for existing records',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        foreach (['incomes', 'expenses', 'funeral_contributions', 'partners', 'partner_payments'] as $tableName) {
            if (! Schema::hasTable($tableName)) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) {
                $table->foreignId('assembly_id')->nullable()->after('id')->constrained('assemblies')->nullOnDelete();
            });

            DB::table($tableName)->whereNull('assembly_id')->update(['assembly_id' => $defaultAssemblyId]);
        }
    }

    public function down(): void
    {
        foreach (['partner_payments', 'partners', 'funeral_contributions', 'expenses', 'incomes'] as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'assembly_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropConstrainedForeignId('assembly_id');
                });
            }
        }

        Schema::dropIfExists('assembly_user');
        Schema::dropIfExists('assemblies');
    }
};
