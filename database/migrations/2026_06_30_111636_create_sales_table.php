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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id');
            $table->foreignId('customer_id');
            $table->integer('voucher_no')->unique();
            $table->decimal('crate_qty', 10, 2)->nullable();
            $table->decimal('total_weight', 10, 2);
            $table->decimal('weight_cut', 10, 2)->nullable();
            $table->decimal('netweight', 10, 2);
            $table->decimal('rate', 10, 2);
            $table->decimal('total_amount', 10, 2);
            $table->date('date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
