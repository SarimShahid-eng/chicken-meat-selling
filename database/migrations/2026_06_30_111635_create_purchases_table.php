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
            $table->string('vehicle_no')->nullable();
            $table->decimal('crate_qty',10,2);
            $table->decimal('total_weight',10,2);
            $table->decimal('weight_cut', 10, 2)->nullable();
            $table->decimal('netweight', 10, 2);
            $table->decimal('rate', 10, 2)->nullable();
            $table->date('rate_date')->nullable();
            $table->decimal('total_amount',10,2)->nullable();
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
