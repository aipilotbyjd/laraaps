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
        Schema::dropIfExists('workflow');

        Schema::create('workflows', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('org_id')->nullable();
            $table->uuid('user_id')->nullable();
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->integer('version')->default(1);
            $table->boolean('active')->default(false);

            // Workflow Definition
            $table->json('nodes');
            $table->json('connections');
            $table->json('settings')->default('{}');

            // Metadata
            $table->json('tags')->nullable();
            $table->uuid('folder_id')->nullable();

            // Scheduling
            $table->json('trigger_config')->nullable();
            $table->string('cron_expression', 100)->nullable();
            $table->string('timezone', 50)->nullable();

            // Performance
            $table->integer('avg_execution_time_ms')->nullable();
            $table->decimal('success_rate', 5, 2)->nullable();
            $table->timestamp('last_execution_at')->nullable();

            // Versioning
            $table->uuid('parent_version_id')->nullable();
            $table->boolean('is_latest')->default(true);

            // Soft delete
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps(); // This handles created_at and updated_at

            // Foreign key constraints
            $table->foreign('org_id')->references('id')->on('organizations')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('parent_version_id')->references('id')->on('workflows')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflows');
    }
};
