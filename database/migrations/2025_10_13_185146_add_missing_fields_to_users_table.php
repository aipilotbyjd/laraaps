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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('org_id')->nullable()->after('id')->constrained('organizations')->onDelete('cascade');
            $table->string('role')->default('user')->after('password');
            $table->string('status')->default('active')->after('role');
            $table->string('avatar')->nullable()->after('status');
            $table->string('timezone')->default('UTC')->after('avatar');
            $table->string('language')->default('en')->after('timezone');
            $table->timestamp('last_login_at')->nullable()->after('email_verified_at');
            $table->boolean('two_factor_enabled')->default(false)->after('remember_token');
            $table->text('two_factor_secret')->nullable()->after('two_factor_enabled');
            $table->text('two_factor_recovery_codes')->nullable()->after('two_factor_secret');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['org_id']);
            $table->dropColumn([
                'org_id',
                'role',
                'status',
                'avatar',
                'timezone',
                'language',
                'last_login_at',
                'two_factor_enabled',
                'two_factor_secret',
                'two_factor_recovery_codes',
            ]);
        });
    }
};
