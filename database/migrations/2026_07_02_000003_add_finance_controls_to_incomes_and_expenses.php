<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('incomes', function (Blueprint $table) {
            $table->foreignId('chart_account_id')->nullable()->after('project_id')->constrained('chart_accounts')->nullOnDelete();
            $table->string('pledge_campaign')->nullable()->after('type');
            $table->string('status')->default('approved')->after('source');
            $table->foreignId('created_by')->nullable()->after('status')->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->after('created_by')->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            $table->foreignId('updated_by')->nullable()->after('approved_at')->constrained('users')->nullOnDelete();
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->foreignId('chart_account_id')->nullable()->after('service_id')->constrained('chart_accounts')->nullOnDelete();
            $table->string('status')->default('approved')->after('category');
            $table->foreignId('created_by')->nullable()->after('status')->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->after('created_by')->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            $table->foreignId('updated_by')->nullable()->after('approved_at')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropConstrainedForeignId('chart_account_id');
            $table->dropConstrainedForeignId('created_by');
            $table->dropConstrainedForeignId('approved_by');
            $table->dropConstrainedForeignId('updated_by');
            $table->dropColumn(['status', 'approved_at']);
        });

        Schema::table('incomes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('chart_account_id');
            $table->dropConstrainedForeignId('created_by');
            $table->dropConstrainedForeignId('approved_by');
            $table->dropConstrainedForeignId('updated_by');
            $table->dropColumn(['pledge_campaign', 'status', 'approved_at']);
        });
    }
};
