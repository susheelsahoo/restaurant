<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_order_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('department_id')->nullable();
            $table->enum('priority', ['normal', 'urgent', 'low'])->default('normal');
            $table->enum('status', ['active', 'draft', 'archived'])->default('active');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('purchase_order_template_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('template_id');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->string('item_name');
            $table->string('category_name')->nullable();
            $table->decimal('default_quantity', 12, 2)->default(1);
            $table->string('unit', 50);
            $table->text('note')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index('template_id');
            $table->index('product_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_order_template_items');
        Schema::dropIfExists('purchase_order_templates');
    }
};
