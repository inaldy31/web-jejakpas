<?php

namespace App\Support\Database;

use Illuminate\Database\Schema\Blueprint;

final class SchemaColumns
{
    public static function publication(Blueprint $table): void
    {
        $table->string('status', 20)->default('draft')->index();
        $table->timestamp('published_at')->nullable();
        self::authors($table);
    }

    public static function authors(Blueprint $table): void
    {
        $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
        $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
    }
}
