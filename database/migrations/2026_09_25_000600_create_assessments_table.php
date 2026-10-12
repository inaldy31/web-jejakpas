<?php

use App\Support\Database\SchemaColumns;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peer_candidate_id')->constrained()->cascadeOnDelete();
            $table->foreignId('upt_id')->constrained();
            $table->foreignId('program_id')->nullable()->constrained()->nullOnDelete();
            $table->string('competency_field', 30)->nullable();
            $table->text('trainings_attended')->nullable();
            $table->text('certifications')->nullable();
            $table->date('assessed_at');
            $table->string('assessor_position')->nullable();
            $table->json('competency_scores');
            $table->json('confidence_scores');
            $table->unsignedTinyInteger('competency_total');
            $table->string('competency_level', 30);
            $table->json('competency_dimension_scores');
            $table->unsignedTinyInteger('confidence_total');
            $table->string('confidence_level', 30);
            $table->json('confidence_section_scores');
            $table->string('suggested_recommendation', 30)->nullable();
            $table->text('history_conclusion')->nullable();
            $table->text('officer_observation')->nullable();
            $table->text('observation_conclusion')->nullable();
            $table->string('final_recommendation', 30)->nullable()->index();
            $table->text('follow_up')->nullable();
            $table->string('approved_by_position')->nullable();
            $table->date('approved_at')->nullable();
            SchemaColumns::authors($table);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['peer_candidate_id', 'assessed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};
