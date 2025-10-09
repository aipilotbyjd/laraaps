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
        Schema::create('node_executions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('execution_id');
            $table->uuid('workflow_id');
            $table->string('node_id', 255);
            $table->string('node_type', 100);

            $table->string('status', 50);
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('finished_at')->nullable();
            $table->integer('execution_time_ms')->nullable();

            // Input/Output
            $table->uuid('input_data_id')->nullable(); // MongoDB reference
            $table->uuid('output_data_id')->nullable();
            $table->text('error')->nullable();

            // Resource tracking
            $table->integer('http_requests_count')->default(0);
            $table->integer('db_queries_count')->default(0);

            $table->timestamp('created_at')->useCurrent();

            // Foreign key constraint
            $table->foreign('execution_id')->references('id')->on('workflow_executions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('node_executions');
    }
};
