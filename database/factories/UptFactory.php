<?php

namespace Database\Factories;

use App\Enums\UptType;
use Illuminate\Database\Eloquent\Factories\Factory;

class UptFactory extends Factory
{
    public function definition(): array
    {
        $city = fake()->unique()->city();

        return [
            'name' => "Lapas Kelas IIA {$city}",
            'short_name' => $city,
            'aliases' => [],
            'type' => UptType::Lapas,
            'province' => fake()->state(),
            'is_pilot' => false,
        ];
    }

    public function pilot(): static
    {
        return $this->state(['is_pilot' => true, 'type' => UptType::Lpp]);
    }
}
