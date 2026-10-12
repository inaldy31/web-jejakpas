<?php

namespace Database\Factories;

use App\Enums\CompetencyField;
use App\Enums\Recommendation;
use App\Models\PeerCandidate;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssessmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'peer_candidate_id' => PeerCandidate::factory(),
            'competency_field' => CompetencyField::Kuliner,
            'assessed_at' => '2026-10-20',
            'assessor_position' => 'Instruktur Pelatihan',
            'competency_scores' => array_fill(1, 12, 4),
            'confidence_scores' => array_fill(1, 20, 4),
        ];
    }

    public function scored(int $competencyEach, int $confidenceEach): static
    {
        return $this->state([
            'competency_scores' => array_fill(1, 12, $competencyEach),
            'confidence_scores' => array_fill(1, 20, $confidenceEach),
        ]);
    }

    public function final(Recommendation $recommendation = Recommendation::Siap): static
    {
        return $this->state([
            'officer_observation' => 'Mampu mendemonstrasikan keterampilan secara runtut.',
            'observation_conclusion' => 'Memadai',
            'final_recommendation' => $recommendation,
            'approved_by_position' => 'Kepala Lapas',
            'approved_at' => '2026-10-21',
        ]);
    }
}
