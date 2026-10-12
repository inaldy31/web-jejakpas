<?php

namespace App\Models;

use App\Models\Concerns\HasPrivateFile;
use App\Models\Concerns\HasPublication;
use App\Models\Concerns\RecordsActivity;
use App\Models\Concerns\TracksAuthors;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PksDocument extends Model
{
    use HasFactory, HasPrivateFile, HasPublication, RecordsActivity, SoftDeletes, TracksAuthors;

    protected $fillable = ['partner_id', 'title', 'pks_number', 'signed_date', 'valid_until', 'file_path', 'notes'];

    protected function casts(): array
    {
        return [
            'signed_date' => 'date',
            'valid_until' => 'date',
        ];
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class)->withTrashed();
    }
}
