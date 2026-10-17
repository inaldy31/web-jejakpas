<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('app_authentication_secret')->nullable();
            $table->text('app_authentication_recovery_codes')->nullable();
            $table->boolean('is_primary_admin')->default(false);
            $table->timestamp('deactivated_at')->nullable();
            $table->boolean('must_change_password')->default(false);
            $table->string('trusted_device_key', 64)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'app_authentication_secret', 'app_authentication_recovery_codes', 'is_primary_admin',
                'deactivated_at', 'must_change_password', 'trusted_device_key',
            ]);
        });
    }
};
