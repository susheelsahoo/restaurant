<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('products')) {
            return;
        }

        DB::statement('ALTER TABLE products CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    }

    public function down(): void
    {
        if (! Schema::hasTable('products')) {
            return;
        }

        DB::statement('ALTER TABLE products CONVERT TO CHARACTER SET latin1 COLLATE latin1_swedish_ci');
    }
};
