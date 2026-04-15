<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('request_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('request_id')->nullable();
            $table->unsignedInteger('product_id')->nullable();
            $table->decimal('quantity', 10, 2)->nullable();
            $table->unsignedInteger('supplier_id')->nullable();
            $table->text('notes')->nullable();

            $table->index('request_id', 'idx_request_items_request');
            $table->index('product_id', 'idx_request_items_product');
            $table->index('supplier_id', 'idx_request_items_supplier');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('request_items');
    }
};
