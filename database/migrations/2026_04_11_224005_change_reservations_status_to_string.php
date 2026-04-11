<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('reservations', 'status')) {
            return;
        }

        DB::statement("ALTER TABLE `reservations` MODIFY COLUMN `status` VARCHAR(50) NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasColumn('reservations', 'status')) {
            return;
        }

        DB::statement("UPDATE `reservations` SET `status` = 'declined' WHERE `status` = 'canceled'");
        DB::statement("ALTER TABLE `reservations` MODIFY COLUMN `status` ENUM('pending','confirmed','declined','complete','in-house') NOT NULL DEFAULT 'pending'");
    }
};
