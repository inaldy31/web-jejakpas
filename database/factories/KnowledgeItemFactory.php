<?php

namespace Database\Factories;

use App\Enums\KnowledgeCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class KnowledgeItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => 'Template PKS JEJAK PAS',
            'category' => KnowledgeCategory::SopPksPedoman,
            'description' => 'Draf template Perjanjian Kerja Sama.',
            'file_path' => 'knowledge/template-pks.pdf',
        ];
    }

    public function published(): static
    {
        return $this->state(['status' => 'published', 'published_at' => now()]);
    }
}
