<?php

namespace Database\Factories;

use App\Enums\PartnerGroup;
use App\Enums\PksStatus;
use App\Enums\SustainabilityStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class PartnerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'group' => PartnerGroup::DuniaUsaha,
            'province' => 'Jawa Barat',
            'contact_position' => 'Manajer Program',
            'contact' => fake()->safeEmail(),
            'pks_status' => PksStatus::Aktif,
            'competency_fields' => ['kuliner'],
            'support_types' => ['pelatihan'],
            'sustainability_status' => SustainabilityStatus::Berlanjut,
        ];
    }

    public function published(): static
    {
        return $this->state(['status' => 'published', 'published_at' => now()]);
    }
}
