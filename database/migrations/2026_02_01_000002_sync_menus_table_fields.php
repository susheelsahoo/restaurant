<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('menus')) {
            return;
        }

        // Add title if missing, copy from name if present
        if (!Schema::hasColumn('menus', 'title')) {
            Schema::table('menus', function (Blueprint $table) {
                $table->string('title')->nullable()->after('id');
            });

            if (Schema::hasColumn('menus', 'name')) {
                DB::statement("UPDATE menus SET title = name WHERE title IS NULL");
            }
        }

        // Add slug if missing
        if (!Schema::hasColumn('menus', 'slug')) {
            Schema::table('menus', function (Blueprint $table) {
                $table->string('slug')->unique()->nullable()->after('title');
            });
        }

        // description
        if (!Schema::hasColumn('menus', 'description')) {
            Schema::table('menus', function (Blueprint $table) {
                $table->text('description')->nullable()->after('slug');
            });
        }

        // price
        if (!Schema::hasColumn('menus', 'price')) {
            Schema::table('menus', function (Blueprint $table) {
                $table->decimal('price', 8, 2)->nullable()->after('description');
            });
        }

        // image
        if (!Schema::hasColumn('menus', 'image')) {
            Schema::table('menus', function (Blueprint $table) {
                $table->string('image')->nullable()->after('price');
            });
        }

        // menu_category_id
        if (!Schema::hasColumn('menus', 'menu_category_id')) {
            Schema::table('menus', function (Blueprint $table) {
                $table->foreignId('menu_category_id')->nullable()->constrained('menu_categories')->nullOnDelete()->after('image');
            });
        }

        // is_active
        if (!Schema::hasColumn('menus', 'is_active')) {
            Schema::table('menus', function (Blueprint $table) {
                $table->boolean('is_active')->default(true)->after('menu_category_id');
            });
        }

        // position
        if (!Schema::hasColumn('menus', 'position')) {
            Schema::table('menus', function (Blueprint $table) {
                $table->integer('position')->default(0)->after('is_active');
            });
        }

        // Remove legacy name column if present (we copied it to title already)
        if (Schema::hasColumn('menus', 'name')) {
            Schema::table('menus', function (Blueprint $table) {
                $table->dropColumn('name');
            });
        }
    }

    public function down()
    {
        // Intentionally left blank (manual rollback recommended)
    }
};
