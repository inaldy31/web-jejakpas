<?php

namespace App\Models;

use App\Enums\SnapshotPhase;
use App\Models\Concerns\HasPublication;
use App\Models\Concerns\RecordsActivity;
use App\Models\Concerns\TracksAuthors;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BaselineSnapshot extends Model
{
    use HasFactory, HasPublication, RecordsActivity, SoftDeletes, TracksAuthors;

    protected $fillable = [
        'upt_id', 'period_label', 'phase', 'total_trainings', 'total_participants', 'total_certified',
        'total_assessed', 'total_peer_educators', 'total_beneficiaries',
        'avg_competency_score', 'avg_confidence_score', 'active_partners', 'notes', 'recorded_at',
    ];

    protected function casts(): array
    {
        return [
            'phase' => SnapshotPhase::class,
            'recorded_at' => 'date',
            'total_trainings' => 'integer',
            'total_participants' => 'integer',
            'total_certified' => 'integer',
            'total_assessed' => 'integer',
            'total_peer_educators' => 'integer',
            'total_beneficiaries' => 'integer',
            'active_partners' => 'integer',
            'avg_competency_score' => 'float',
            'avg_confidence_score' => 'float',
        ];
    }

    public function upt(): BelongsTo
    {
        return $this->belongsTo(Upt::class);
    }
}
