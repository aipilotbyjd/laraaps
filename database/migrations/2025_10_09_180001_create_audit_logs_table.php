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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('org_id');
            $table->uuid('user_id')->nullable();

            $table->string('action', 100);
            $table->string('resource_type', 50);
            $table->uuid('resource_id')->nullable();

            $table->json('changes')->nullable();
            $table->string('ip_address', 45)->nullable(); // Using string instead of INET
            $table->text('user_agent')->nullable();

            $table->timestamp('created_at')->useCurrent();

            // Foreign key constraints
            $table->foreign('org_id')->references('id')->on('organizations')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
