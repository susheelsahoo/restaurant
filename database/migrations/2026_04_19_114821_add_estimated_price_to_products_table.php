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
        if (! Schema::hasTable('products') || Schema::hasColumn('products', 'estimated_price')) {
            return;
        }

        Schema::table('products', function (Blueprint $table) {
            $table->decimal('estimated_price', 10, 2)->nullable()->after('barcode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('products') || ! Schema::hasColumn('products', 'estimated_price')) {
            return;
        }

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('estimated_price');
        });
    }
};
