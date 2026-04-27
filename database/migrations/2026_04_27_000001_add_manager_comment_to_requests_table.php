<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('requests', 'manager_comment')) {
            return;
        }

        Schema::table('requests', function (Blueprint $table) {
            $table->text('manager_comment')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        if (!Schema::hasColumn('requests', 'manager_comment')) {
            return;
        }

        Schema::table('requests', function (Blueprint $table) {
            $table->dropColumn('manager_comment');
        });
    }
};
