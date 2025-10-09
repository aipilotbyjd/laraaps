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
        Schema::dropIfExists('notification');

        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('recipient_id'); // User who receives the notification
            $table->string('type', 100); // workflow_success, workflow_error, schedule_missed, etc.
            $table->string('title', 255);
            $table->text('message');
            $table->json('data')->nullable(); // Additional data related to the notification
            $table->boolean('read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->uuid('related_entity_id')->nullable(); // Related workflow, execution, etc.
            $table->string('related_entity_type', 50)->nullable(); // workflow, execution, etc.
            $table->timestamp('created_at')->useCurrent();

            // Foreign key constraint
            $table->foreign('recipient_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
