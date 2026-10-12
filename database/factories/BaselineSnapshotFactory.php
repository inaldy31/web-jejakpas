<?php

namespace Database\Factories;

use App\Enums\SnapshotPhase;
use App\Models\Upt;
use Illuminate\Database\Eloquent\Factories\Factory;

class BaselineSnapshotFactory extends Factory
{
    public function definition(): array
    {
        return [
            'upt_id' => Upt::factory()->pilot(),
            'period_label' => '2024–2026',
            'phase' => SnapshotPhase::Baseline,
            'total_participants' => 400,
            'total_certified' => 200,
            'recorded_at' => '2026-09-04',
        ];
    }

    public function post(): static
    {
        return $this->state(['phase' => SnapshotPhase::Post, 'period_label' => 'Okt–Nov 2026', 'recorded_at' => '2026-11-03']);
    }

    public function published(): static
    {
        return $this->state(['status' => 'published', 'published_at' => now()]);
    }
}
