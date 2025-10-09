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
        Schema::dropIfExists('team');

        Schema::create('teams', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('org_id');
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->json('settings')->default('{}');
            $table->timestamps(); // This handles created_at and updated_at

            // Foreign key constraint
            $table->foreign('org_id')->references('id')->on('organizations')->onDelete('cascade');
        });

        // Create pivot table for team members
        Schema::create('team_members', function (Blueprint $table) {
            $table->uuid('team_id');
            $table->uuid('user_id');
            $table->string('role', 50)->default('member'); // owner, admin, member
            $table->timestamp('added_at')->useCurrent();

            $table->primary(['team_id', 'user_id']);
            $table->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_members');
        Schema::dropIfExists('teams');
    }
};
