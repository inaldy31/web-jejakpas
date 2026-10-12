<?php

namespace App\Models;

use App\Enums\CertificationStatus;
use App\Enums\CompetencyField;
use App\Models\Concerns\HasPublication;
use App\Models\Concerns\RecordsActivity;
use App\Models\Concerns\TracksAuthors;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Program extends Model
{
    use HasFactory, HasPublication, RecordsActivity, SoftDeletes, TracksAuthors;

    protected $fillable = [
        'upt_id', 'year', 'name', 'competency_field', 'partner_id', 'instructor_note',
        'total_participants', 'total_certified', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'competency_field' => CompetencyField::class,
            'certification_status' => CertificationStatus::class,
            'year' => 'integer',
            'total_participants' => 'integer',
            'total_certified' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Program $program): void {
            $program->certification_status = CertificationStatus::fromCount($program->total_certified);
        });

        static::forceDeleting(function (Program $program): void {
            PeerCandidate::withTrashed()->where('program_id', $program->id)->get()
                ->each(fn (PeerCandidate $candidate) => $candidate->update(['program_id' => null]));

            Assessment::withTrashed()->where('program_id', $program->id)->get()
                ->each(fn (Assessment $assessment) => $assessment->update(['program_id' => null]));
        });
    }

    public function upt(): BelongsTo
    {
        return $this->belongsTo(Upt::class);
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }
}
