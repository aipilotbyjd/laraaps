<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('credentials', function (Blueprint $table) {
            $table->uuid('id')->primary()->defaultRaw('gen_random_uuid()');
            $table->uuid('org_id')->nullable();
            $table->uuid('user_id')->nullable();
            $table->string('name', 255);
            $table->string('type', 100);

            // Encrypted data (AES-256-GCM)
            $table->text('encrypted_data');
            $table->string('encryption_key_id', 100); // KMS key reference

            // Test status
            $table->timestampTz('last_tested_at')->nullable();
            $table->string('test_status', 50)->nullable();

            // Access control
            $table->json('shared_with_users')->nullable(); // Using JSON instead of UUID array

            $table->timestampTz('created_at')->default(DB::raw('NOW()'));
            $table->timestampTz('updated_at')->default(DB::raw('NOW()'));

            // Foreign key constraints
            $table->foreign('org_id')->references('id')->on('organizations')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credentials');
    }
};
