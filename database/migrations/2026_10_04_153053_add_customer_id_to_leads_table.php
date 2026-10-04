<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
     public function up(): void
    {
        if (Schema::hasTable('leads') && !Schema::hasColumn('leads', 'customer_id')) {
            Schema::table('leads', function (Blueprint $table) {
                $table->foreignId('customer_id')
                    ->nullable()
                    ->after('notes')
                    ->before('created_at')
                    ->constrained('customers')
                    ->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('leads') && Schema::hasColumn('leads', 'customer_id')) {
            Schema::table('leads', function (Blueprint $table) {
                $table->dropForeign(['customer_id']);
                $table->dropColumn('customer_id');
            });
        }
    }
};
