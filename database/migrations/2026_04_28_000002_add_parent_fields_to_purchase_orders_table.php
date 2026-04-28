<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('purchase_orders')) {
            return;
        }

        Schema::table('purchase_orders', function (Blueprint $table) {
            if (! Schema::hasColumn('purchase_orders', 'parent_po_id')) {
                $table->unsignedBigInteger('parent_po_id')->nullable()->after('po_number');
                $table->index('parent_po_id', 'purchase_orders_parent_po_id_idx');
            }

            if (! Schema::hasColumn('purchase_orders', 'po_suffix')) {
                $table->string('po_suffix', 10)->nullable()->after('parent_po_id');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('purchase_orders')) {
            return;
        }

        Schema::table('purchase_orders', function (Blueprint $table) {
            if (Schema::hasColumn('purchase_orders', 'parent_po_id')) {
                $table->dropIndex('purchase_orders_parent_po_id_idx');
                $table->dropColumn('parent_po_id');
            }

            if (Schema::hasColumn('purchase_orders', 'po_suffix')) {
                $table->dropColumn('po_suffix');
            }
        });
    }
};
