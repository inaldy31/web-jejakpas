<?php

namespace Database\Seeders;

use App\Models\Partner;
use App\Models\Upt;
use Illuminate\Database\Seeder;

class PartnerSeeder extends Seeder
{
    public function run(): void
    {
        $partners = [
            ['M-0001', 'CV. Jaya Makmur', 'dunia_usaha', 'Pelatihan Warga Binaan: 2 kegiatan (2025)', ['kriya'], UptSeeder::BANDUNG, 2, 40, 40],
            ['M-0002', 'Rumah Berbagi', 'ngo_komunitas', 'Pelatihan Warga Binaan: 10 kegiatan (2024–2026)', ['kuliner'], UptSeeder::BANDUNG, 10, 200, 20],
            ['M-0003', 'Syakira Craft', 'dunia_usaha', 'Pelatihan Warga Binaan: 1 kegiatan (2025)', ['kriya'], UptSeeder::BANDUNG, 1, 20, 20],
            ['M-0004', 'Universitas Budi Luhur', 'perguruan_tinggi', 'Pelatihan Warga Binaan: 5 kegiatan (2026)', ['kecantikan', 'kuliner', 'lainnya'], UptSeeder::BANDUNG, 5, 120, 120],
            ['M-0005', 'Pertamina', 'bumn', 'Pelatihan Warga Binaan: 3 kegiatan (2023)', ['fesyen', 'kriya', 'kuliner'], UptSeeder::BENGKULU, 3, 51, null],
            ['M-0006', 'Attempe Healthy & Natural', 'dunia_usaha', 'Pelatihan Warga Binaan: 1 kegiatan (2025)', ['kuliner'], UptSeeder::YOGYAKARTA, 1, 30, 30],
            ['M-0007', 'BLK Gunungkidul', 'lembaga_pendidikan', 'Pelatihan Warga Binaan: 3 kegiatan (2025)', ['fesyen', 'keterampilan_digital', 'lainnya'], UptSeeder::YOGYAKARTA, 3, 50, 50],
            ['M-0008', 'CV. Banyu Sabrang Batik', 'dunia_usaha', 'Pelatihan Warga Binaan: 3 kegiatan (2023–2025)', ['fesyen', 'kriya'], UptSeeder::YOGYAKARTA, 3, 49, 20],
            ['M-0009', 'Duarte Salon Hair Stylish & Make Up Artist', 'dunia_usaha', 'Pelatihan Warga Binaan: 6 kegiatan (2025)', ['kecantikan'], UptSeeder::YOGYAKARTA, 6, 100, 100],
            ['M-0010', 'Grand Senyum Hotel', 'dunia_usaha', 'Pelatihan Warga Binaan: 2 kegiatan (2025)', ['kecantikan', 'kuliner'], UptSeeder::YOGYAKARTA, 2, 67, 67],
            ['M-0011', 'Kimbis Klinik Iptek Mina Bisnis', 'lembaga_pendidikan', 'Pelatihan Warga Binaan: 1 kegiatan (2023)', ['lainnya'], UptSeeder::YOGYAKARTA, 1, 10, 1],
            ['M-0012', 'Kinan Earth', 'dunia_usaha', 'Pelatihan Warga Binaan: 1 kegiatan (2025)', ['lainnya'], UptSeeder::YOGYAKARTA, 1, 30, 30],
            ['M-0013', 'LPK Hiska', 'lembaga_pendidikan', 'Pelatihan Warga Binaan: 5 kegiatan (2023–2024)', ['fesyen', 'kriya', 'kuliner'], UptSeeder::YOGYAKARTA, 5, 55, 55],
            ['M-0014', 'Nanas Bagong Bu Babin', 'dunia_usaha', 'Pelatihan Warga Binaan: 1 kegiatan (2025)', ['lainnya'], UptSeeder::YOGYAKARTA, 1, 32, null],
        ];

        foreach ($partners as [$code, $name, $group, $scope, $fields, $uptName, $trainings, $participants, $certified]) {
            $partner = Partner::withTrashed()->firstOrCreate(['code' => $code], [
                'name' => $name,
                'group' => $group,
                'scope' => $scope,
                'competency_fields' => $fields,
                'support_types' => ['pelatihan'],
                'total_trainings' => $trainings,
                'total_participants' => $participants,
                'total_certified' => $certified,
            ]);

            if ($partner->wasRecentlyCreated) {
                $partner->upts()->sync([Upt::where('name', $uptName)->value('id')]);
            }
        }
    }
}
