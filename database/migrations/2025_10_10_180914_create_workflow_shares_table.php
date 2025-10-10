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
        Schema::create('workflow_shares', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('workflow_id');
            $table->uuid('user_id');
            $table->string('permissions');
            $table->timestamps();

            $table->foreign('workflow_id')->references('id')->on('workflows')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_shares');
    }
};
