<?php

use App\Support\Database\SchemaColumns;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('baseline_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('upt_id')->constrained();
            $table->string('period_label', 60);
            $table->string('phase', 20);
            $table->unsignedInteger('total_trainings')->nullable();
            $table->unsignedInteger('total_participants')->nullable();
            $table->unsignedInteger('total_certified')->nullable();
            $table->unsignedInteger('total_assessed')->nullable();
            $table->unsignedInteger('total_peer_educators')->nullable();
            $table->unsignedInteger('total_beneficiaries')->nullable();
            $table->decimal('avg_competency_score', 5, 2)->nullable();
            $table->decimal('avg_confidence_score', 5, 2)->nullable();
            $table->unsignedInteger('active_partners')->nullable();
            $table->text('notes')->nullable();
            $table->date('recorded_at');
            SchemaColumns::publication($table);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['upt_id', 'phase', 'recorded_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('baseline_snapshots');
    }
};
