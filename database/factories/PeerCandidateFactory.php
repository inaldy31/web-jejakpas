<?php

namespace Database\Factories;

use App\Enums\CompetencyField;
use App\Models\Upt;
use Illuminate\Database\Eloquent\Factories\Factory;

class PeerCandidateFactory extends Factory
{
    public function definition(): array
    {
        return [
            'upt_id' => Upt::factory(),
            'label' => strtoupper(fake()->lexify('??')),
            'competency_field' => CompetencyField::Kuliner,
        ];
    }
}
