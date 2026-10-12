<?php

use App\Support\Database\SchemaColumns;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partners', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name');
            $table->string('group', 40)->nullable();
            $table->string('province')->nullable();
            $table->string('contact_position')->nullable();
            $table->string('contact')->nullable();
            $table->string('pks_number')->nullable();
            $table->date('pks_date')->nullable();
            $table->date('pks_valid_until')->nullable();
            $table->text('scope')->nullable();
            $table->string('pks_status', 20)->nullable();
            $table->json('competency_fields')->nullable();
            $table->json('support_types')->nullable();
            $table->unsignedInteger('total_trainings')->nullable();
            $table->unsignedInteger('total_participants')->nullable();
            $table->unsignedInteger('total_certified')->nullable();
            $table->unsignedInteger('total_peer_educators')->nullable();
            $table->unsignedInteger('total_beneficiaries')->nullable();
            $table->decimal('avg_confidence_score', 5, 2)->nullable();
            $table->string('sustainability_status', 20)->nullable();
            $table->text('best_practice_summary')->nullable();
            $table->string('liaison_position')->nullable();
            SchemaColumns::publication($table);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('partner_upt', function (Blueprint $table) {
            $table->foreignId('partner_id')->constrained()->cascadeOnDelete();
            $table->foreignId('upt_id')->constrained()->cascadeOnDelete();
            $table->primary(['partner_id', 'upt_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partner_upt');
        Schema::dropIfExists('partners');
    }
};
