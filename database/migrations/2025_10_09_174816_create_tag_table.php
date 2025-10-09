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
        Schema::dropIfExists('tag');

        Schema::create('tags', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 255);
            $table->uuid('org_id')->nullable();
            $table->uuid('created_by')->nullable();
            $table->timestamps(); // This handles created_at and updated_at

            // Foreign key constraints
            $table->foreign('org_id')->references('id')->on('organizations')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });

        // Create pivot table for workflow tags
        Schema::create('workflow_tag', function (Blueprint $table) {
            $table->uuid('workflow_id');
            $table->uuid('tag_id');

            $table->primary(['workflow_id', 'tag_id']);
            $table->foreign('workflow_id')->references('id')->on('workflows')->onDelete('cascade');
            $table->foreign('tag_id')->references('id')->on('tags')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_tag');
        Schema::dropIfExists('tags');
    }
};
