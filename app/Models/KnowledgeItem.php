<?php

namespace App\Models;

use App\Enums\KnowledgeCategory;
use App\Models\Concerns\HasPrivateFile;
use App\Models\Concerns\HasPublication;
use App\Models\Concerns\RecordsActivity;
use App\Models\Concerns\TracksAuthors;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KnowledgeItem extends Model
{
    use HasFactory, HasPrivateFile, HasPublication, RecordsActivity, SoftDeletes, TracksAuthors;

    protected $fillable = ['title', 'category', 'description', 'file_path'];

    protected function casts(): array
    {
        return ['category' => KnowledgeCategory::class];
    }
}
