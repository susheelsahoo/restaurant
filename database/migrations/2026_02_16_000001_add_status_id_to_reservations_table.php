<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\ReservationStatus;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            // Add status_id column if it doesn't exist
            if (!Schema::hasColumn('reservations', 'status_id')) {
                $table->unsignedBigInteger('status_id')->nullable()->after('id');
            }
        });

        // Migrate existing status values to the new table
        $statuses = ReservationStatus::all()->keyBy('name');
        
        DB::table('reservations')
            ->whereNotNull('status')
            ->orderBy('id')
            ->each(function ($reservation) use ($statuses) {
                $statusId = $statuses[$reservation->status]->id ?? null;
                if ($statusId) {
                    DB::table('reservations')
                        ->where('id', $reservation->id)
                        ->update(['status_id' => $statusId]);
                }
            });

        // Add foreign key constraint
        Schema::table('reservations', function (Blueprint $table) {
            if (!Schema::hasColumn('reservations', 'status_id')) {
                return;
            }
            
            // Check if the foreign key already exists
            try {
                $table->foreign('status_id')
                    ->references('id')
                    ->on('reservation_statuses')
                    ->onDelete('restrict');
            } catch (\Exception $e) {
                // Foreign key might already exist
            }
        });

        // Drop the old status column (optional - keep for backward compatibility)
        // Schema::table('reservations', function (Blueprint $table) {
        //     $table->dropColumn('status');
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            // Drop foreign key if it exists
            try {
                $table->dropForeign(['status_id']);
            } catch (\Exception $e) {
                // Foreign key doesn't exist
            }
            
            // Drop the status_id column
            if (Schema::hasColumn('reservations', 'status_id')) {
                $table->dropColumn('status_id');
            }
        });
    }
};
