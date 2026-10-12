<?php

use App\Support\Database\SchemaColumns;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peer_candidates', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->foreignId('upt_id')->constrained();
            $table->string('label', 50);
            $table->string('competency_field', 30)->nullable();
            $table->foreignId('program_id')->nullable()->constrained()->nullOnDelete();
            $table->string('current_status', 30)->default('calon')->index();
            $table->date('designated_at')->nullable();
            $table->string('approved_by_position')->nullable();
            $table->text('notes')->nullable();
            SchemaColumns::authors($table);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peer_candidates');
    }
};
