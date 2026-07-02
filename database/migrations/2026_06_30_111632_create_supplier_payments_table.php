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
        Schema::create('supplier_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_id')->nullable();
            $table->foreignId('supplier_id');
            $table->integer('amount')->nullable();
            $table->longText('description')->nullable();
            $table->date('date');
            $table->enum('payment_type', ["credit","debit"])->default('credit');
            $table->enum('type', ["bank","cash"]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_payments');
    }
};
