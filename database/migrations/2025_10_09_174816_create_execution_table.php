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
        Schema::dropIfExists('execution');

        Schema::create('workflow_executions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('workflow_id');
            $table->uuid('org_id');
            $table->uuid('user_id')->nullable();

            // Execution metadata
            $table->string('mode', 50); // manual, webhook, schedule, retry, test
            $table->string('status', 50); // queued, running, success, error, cancelled, waiting

            // Timing
            $table->timestamp('queued_at')->useCurrent(); // Use current timestamp
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamp('wait_until')->nullable();

            // Performance metrics
            $table->integer('execution_time_ms')->nullable();
            $table->integer('node_executions_count')->nullable();

            // Data & Results
            $table->json('trigger_data')->nullable();
            $table->uuid('execution_data_id')->nullable(); // Reference to MongoDB for large data
            $table->text('error_message')->nullable();
            $table->text('error_stack')->nullable();

            // Resource usage
            $table->integer('memory_used_mb')->nullable();
            $table->integer('cpu_time_ms')->nullable();

            // Retry logic
            $table->integer('retry_count')->default(0);
            $table->uuid('parent_execution_id')->nullable();

            // Billing
            $table->integer('credits_used')->nullable();

            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_executions');
    }
};
