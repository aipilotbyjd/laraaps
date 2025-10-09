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
        Schema::dropIfExists('webhook');

        Schema::create('webhooks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('workflow_id');
            $table->string('node_id', 255);
            $table->uuid('org_id');

            $table->string('method', 10);
            $table->string('path', 500)->unique();

            // Security
            $table->string('auth_type', 50)->nullable(); // none, basic, header, query
            $table->json('auth_config')->nullable();
            $table->json('ip_whitelist')->nullable(); // Using JSON instead of INET array

            // Performance
            $table->timestamp('last_triggered_at')->nullable();
            $table->bigInteger('trigger_count')->default(0);

            $table->boolean('active')->default(true);
            $table->timestamp('created_at')->useCurrent();

            // Foreign key constraint
            $table->foreign('workflow_id')->references('id')->on('workflows')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('webhooks');
    }
};
