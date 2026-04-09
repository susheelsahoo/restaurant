<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('po_number')->unique();
            $table->unsignedBigInteger('request_id')->nullable();
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->unsignedBigInteger('buyer_id')->nullable();
            $table->enum('status', ['draft', 'sent', 'confirmed', 'partial', 'completed', 'delayed'])->default('draft');
            $table->date('order_date');
            $table->date('expected_delivery')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
