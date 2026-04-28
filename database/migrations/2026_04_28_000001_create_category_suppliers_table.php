<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('category_suppliers')) {
            return;
        }

        Schema::create('category_suppliers', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('supplier_id');

            $table->unique(['category_id', 'supplier_id'], 'category_suppliers_unique');
            $table->index('supplier_id', 'category_suppliers_supplier_idx');
        });

        if (Schema::hasTable('products') && Schema::hasTable('product_suppliers')) {
            DB::table('category_suppliers')->insertUsing(
                ['category_id', 'supplier_id'],
                DB::table('products')
                    ->join('product_suppliers', 'product_suppliers.product_id', '=', 'products.id')
                    ->whereNotNull('products.category_id')
                    ->distinct()
                    ->select('products.category_id', 'product_suppliers.supplier_id')
            );
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('category_suppliers');
    }
};
