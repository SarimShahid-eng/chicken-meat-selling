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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id');
            $table->foreignId('supplier_id');
            $table->integer('voucher_no')->unique();
            $table->integer('vehicle_no')->nullable();
            $table->integer('crate_qty');
            $table->integer('total_weight');
            $table->integer('weight_cut')->nullable();
            $table->integer('netweight');
            $table->integer('rate')->nullable();
            $table->date('rate_date');
            $table->integer('total_amount')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
