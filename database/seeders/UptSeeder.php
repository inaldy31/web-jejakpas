<?php

namespace Database\Seeders;

use App\Enums\UptType;
use App\Models\Upt;
use Illuminate\Database\Seeder;

class UptSeeder extends Seeder
{
    public const BANDUNG = 'Lapas Perempuan Kelas IIA Bandung';

    public const BENGKULU = 'Lapas Perempuan Kelas IIB Bengkulu';

    public const YOGYAKARTA = 'Lapas Perempuan Kelas IIB Yogyakarta';

    public function run(): void
    {
        $upts = [
            [self::BANDUNG, 'Bandung', 'Jawa Barat', ['LPP Kelas IIA Bandung', 'LPP Bandung', 'Lapas Perempuan Bandung']],
            [self::BENGKULU, 'Bengkulu', 'Bengkulu', ['LPP Bengkulu', 'LPP Kelas IIB Bengkulu']],
            [self::YOGYAKARTA, 'Yogyakarta', 'D.I. Yogyakarta', ['LPP Yogyakarta', 'LPP Kelas IIB Yogyakarta']],
        ];

        foreach ($upts as [$name, $shortName, $province, $aliases]) {
            Upt::firstOrCreate(['name' => $name], [
                'short_name' => $shortName,
                'aliases' => $aliases,
                'type' => UptType::Lpp,
                'province' => $province,
                'is_pilot' => true,
            ]);
        }
    }
}
