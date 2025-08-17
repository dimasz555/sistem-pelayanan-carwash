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
        Schema::table('transactions', function (Blueprint $table) {
            $table->decimal('service_price', 10, 2)->after('service_id')->nullable(); // Harga asli layanan
            $table->decimal('discount_amount', 10, 2)->after('service_price')->nullable()->default(0); // Nominal diskon (dalam Rupiah)
            $table->decimal('total_price', 10, 2)->after('discount_amount')->nullable(); // Total setelah diskon
            $table->string('discount_reason', 100)->after('total_price')->nullable(); // Alasan diskon (opsional)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['service_price', 'discount_amount', 'total_price', 'discount_reason']);
        });
    }
};
