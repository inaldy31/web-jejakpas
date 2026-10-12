<?php

namespace Database\Seeders;

use App\Enums\SnapshotPhase;
use App\Models\BaselineSnapshot;
use App\Models\Upt;
use Illuminate\Database\Seeder;

class BaselineSnapshotSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            [UptSeeder::BANDUNG, '2024–2026', 19, 400, 200, 4,
                'Baseline ARUNIKA (dataset terpisah, skala Self Confidence Questionnaire lama): 30 Warga Binaan tersertifikasi, '
                .'rentang skor 78–133, rata-rata 107,07, 10 orang skor < 100. Tidak sebanding langsung dengan instrumen JEJAK PAS (20–100).'],
            [UptSeeder::BENGKULU, '2023–2026', 16, 271, 60, 1,
                'Tren peserta pelatihan menurun dari 106 (2023) menjadi 15 (2026).'],
            [UptSeeder::YOGYAKARTA, '2023–2025', 23, 423, 353, 9, null],
        ];

        foreach ($rows as [$uptName, $period, $trainings, $participants, $certified, $partners, $notes]) {
            $upt = Upt::where('name', $uptName)->firstOrFail();

            BaselineSnapshot::withTrashed()->firstOrCreate(
                ['upt_id' => $upt->id, 'phase' => SnapshotPhase::Baseline->value],
                [
                    'period_label' => $period,
                    'total_trainings' => $trainings,
                    'total_participants' => $participants,
                    'total_certified' => $certified,
                    'active_partners' => $partners,
                    'notes' => $notes,
                    'recorded_at' => '2026-09-04',
                ],
            );
        }
    }
}
