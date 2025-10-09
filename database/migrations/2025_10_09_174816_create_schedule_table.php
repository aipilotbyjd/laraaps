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
        Schema::dropIfExists('schedule');

        Schema::create('schedules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('workflow_id');
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->string('cron_expression', 100);
            $table->string('timezone', 50)->default('UTC');
            $table->boolean('active')->default(true);
            $table->timestamp('last_execution_at')->nullable();
            $table->timestamp('next_execution_at')->nullable();
            $table->json('execution_data')->nullable();
            $table->uuid('created_by')->nullable();
            $table->timestamps(); // This handles created_at and updated_at

            // Foreign key constraint
            $table->foreign('workflow_id')->references('id')->on('workflows')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
