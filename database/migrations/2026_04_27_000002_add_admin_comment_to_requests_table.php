<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('requests', 'admin_comment')) {
            return;
        }

        Schema::table('requests', function (Blueprint $table) {
            $table->text('admin_comment')->nullable()->after('manager_comment');
        });
    }

    public function down(): void
    {
        if (!Schema::hasColumn('requests', 'admin_comment')) {
            return;
        }

        Schema::table('requests', function (Blueprint $table) {
            $table->dropColumn('admin_comment');
        });
    }
};
