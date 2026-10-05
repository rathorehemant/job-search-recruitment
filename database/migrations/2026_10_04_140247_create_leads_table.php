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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('company')->nullable();
            $table->enum('source', [
                'Web',
                'Ads',
                'Referral',
            ]);

            $table->enum('status', [
                'New',
                'In Progress',
                'Won',
                'Lost',
            ])->default('New');

            $table->foreignId('assigned_to')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();


            $table->date('follow_up_date')->nullable();

            $table->text('notes')->nullable();

            // $table->foreignId('customer_id')
            //     ->nullable()
            //     ->constrained('customers')
            //     ->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
