<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('upts', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('short_name', 60);
            $table->json('aliases')->nullable();
            $table->string('type', 20);
            $table->string('province')->nullable();
            $table->boolean('is_pilot')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('upts');
    }
};
