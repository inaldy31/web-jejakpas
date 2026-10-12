<?php

use App\Support\Database\SchemaColumns;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('upt_id')->constrained();
            $table->unsignedSmallInteger('year');
            $table->string('name');
            $table->string('competency_field', 30)->nullable();
            $table->foreignId('partner_id')->nullable()->constrained()->nullOnDelete();
            $table->string('instructor_note')->nullable();
            $table->unsignedInteger('total_participants')->nullable();
            $table->unsignedInteger('total_certified')->nullable();
            $table->string('certification_status', 30);
            $table->text('notes')->nullable();
            SchemaColumns::publication($table);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['upt_id', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};
