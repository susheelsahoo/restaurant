<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_no', 50)->unique();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->enum('priority', ['low', 'normal', 'urgent'])->default('normal');
            $table->enum('status', ['submitted', 'approved', 'rejected', 'ordered', 'returned'])->default('submitted');
            $table->dateTime('needed_by')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('user_id', 'idx_request_user');
            $table->index('department_id', 'idx_request_department');
            $table->index('status', 'idx_request_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};
