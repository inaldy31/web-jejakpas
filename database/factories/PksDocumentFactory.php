<?php

namespace Database\Factories;

use App\Models\Partner;
use Illuminate\Database\Eloquent\Factories\Factory;

class PksDocumentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'partner_id' => Partner::factory(),
            'title' => 'PKS '.fake()->company(),
            'pks_number' => 'PKS/'.fake()->numerify('##').'/2026',
            'signed_date' => '2026-03-01',
            'valid_until' => '2028-03-01',
            'file_path' => 'pks/contoh.pdf',
        ];
    }
}
