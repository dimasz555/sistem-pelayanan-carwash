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
            // Add promo relationship
            $table->foreignId('promo_id')->nullable()->constrained('promos')->nullOnDelete()->after('service_id');
            $table->decimal('promo_discount', 10, 2)->default(0)->after('promo_id');

            // Remove old discount columns
            $table->dropColumn(['discount_amount', 'discount_reason']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Restore old discount columns
            $table->decimal('discount_amount', 10, 2)->default(0)->after('service_price');
            $table->string('discount_reason', 100)->nullable()->after('discount_amount');

            // Remove promo columns
            $table->dropForeign(['promo_id']);
            $table->dropColumn(['promo_id', 'promo_discount']);
        });
    }
};
