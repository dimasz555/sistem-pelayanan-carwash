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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('cashier_name');
            $table->string('invoice')->unique();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('restrict');
            $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();
            $table->unsignedInteger('queue_number');
            $table->string('plate_number');
            $table->string('vehicle_name');
            $table->enum('status', ['menunggu', 'proses', 'selesai'])->default('menunggu');
            $table->boolean('is_free')->default(false);
            $table->datetime('transaction_at');
            $table->datetime('waiting_at')->nullable();
            $table->datetime('processing_at')->nullable();
            $table->datetime('done_at')->nullable();
            $table->boolean('is_paid')->default(false);
            $table->datetime('paid_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
