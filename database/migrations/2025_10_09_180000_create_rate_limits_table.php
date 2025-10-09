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
        Schema::create('rate_limits', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('org_id');
            $table->string('resource_type', 50); // api, workflow_execution, webhook

            $table->integer('limit_per_minute')->nullable();
            $table->integer('limit_per_hour')->nullable();
            $table->integer('limit_per_day')->nullable();

            $table->integer('current_usage_minute')->default(0);
            $table->integer('current_usage_hour')->default(0);
            $table->integer('current_usage_day')->default(0);

            $table->timestamp('window_start')->nullable();

            $table->unique(['org_id', 'resource_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rate_limits');
    }
};
