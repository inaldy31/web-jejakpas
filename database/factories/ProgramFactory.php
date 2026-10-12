<?php

namespace Database\Factories;

use App\Enums\CompetencyField;
use App\Models\Upt;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProgramFactory extends Factory
{
    public function definition(): array
    {
        return [
            'upt_id' => Upt::factory(),
            'year' => 2025,
            'name' => 'Pelatihan '.fake()->words(2, true),
            'competency_field' => CompetencyField::Kuliner,
            'total_participants' => 20,
            'total_certified' => 20,
        ];
    }

    public function published(): static
    {
        return $this->state(['status' => 'published', 'published_at' => now()]);
    }
}
