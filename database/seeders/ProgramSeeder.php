<?php

namespace Database\Seeders;

use App\Enums\CertificationStatus;
use App\Models\Partner;
use App\Models\Program;
use App\Models\Upt;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProgramSeeder extends Seeder
{
    private const NO_DATA = 'Data sertifikasi/mitra tidak dicantumkan pada sumber.';

    private const PERTAMINA = 'Dukungan Pertamina; data sertifikasi tidak dicantumkan pada sumber.';

    private const DASH = "Sertifikasi ditandai '-' pada sumber.";

    private const NO_INSTRUCTOR = 'Instruktur tidak tercantum pada sumber.';

    public function run(): void
    {
        if (Program::withTrashed()->exists()) {
            $this->command?->warn(__('jejak.programs.seed_skipped_warning'));

            return;
        }

        $upts = [
            'bdg' => UptSeeder::BANDUNG,
            'bkl' => UptSeeder::BENGKULU,
            'ygy' => UptSeeder::YOGYAKARTA,
        ];

        $rows = [
            ['bdg', 2024, 'Membuat Choco and Fruit Cookies', 'kuliner', 'M-0002', 20, 0, null],
            ['bdg', 2024, 'Membuat Kue Lumpur Surga', 'kuliner', 'M-0002', 20, 0, null],
            ['bdg', 2024, 'Membuat Chicken Steak', 'kuliner', 'M-0002', 20, 0, null],
            ['bdg', 2025, 'Membatik', 'kriya', 'M-0001', 20, 20, null],
            ['bdg', 2025, 'Eco Print', 'kriya', 'M-0001', 20, 20, null],
            ['bdg', 2025, 'Kerajinan Kulit (tas, dompet, topi, dsb.)', 'kriya', 'M-0003', 20, 20, null],
            ['bdg', 2025, 'Cake Decoration dan Jajanan Basah', 'kuliner', 'M-0002', 20, 20, null],
            ['bdg', 2025, 'Curving Buah', 'kuliner', 'M-0002', 20, 0, null],
            ['bdg', 2025, 'Bolu Kukus', 'kuliner', 'M-0002', 20, 0, null],
            ['bdg', 2025, 'Banana Cheese Bread dan Browkies', 'kuliner', 'M-0002', 20, 0, null],
            ['bdg', 2025, 'Membuat Jamu', 'kuliner', 'M-0002', 20, 0, null],
            ['bdg', 2026, 'Membuat Frozen Food', 'kuliner', null, 20, 0, self::NO_INSTRUCTOR],
            ['bdg', 2026, 'Cake Tape dan Marble Cake', 'kuliner', 'M-0002', 20, 0, null],
            ['bdg', 2026, 'Membuat Pudding Cup', 'kuliner', 'M-0002', 20, 0, null],
            ['bdg', 2026, 'Membuat Lilin Aromaterapi', 'lainnya', 'M-0004', 20, 20, null],
            ['bdg', 2026, 'Make Up dan Hair Do', 'kecantikan', 'M-0004', 40, 40, null],
            ['bdg', 2026, 'Membuat Parfume', 'kecantikan', 'M-0004', 20, 20, null],
            ['bdg', 2026, 'Membuat Eco Enzim', 'lainnya', 'M-0004', 20, 20, null],
            ['bdg', 2026, 'Barista', 'kuliner', 'M-0004', 20, 20, null],

            ['bkl', 2023, 'Tata Rias', 'kecantikan', null, 15, null, self::NO_DATA],
            ['bkl', 2023, 'Tata Boga', 'kuliner', 'M-0005', 20, null, self::PERTAMINA],
            ['bkl', 2023, 'Membatik', 'kriya', 'M-0005', 11, null, self::PERTAMINA],
            ['bkl', 2023, 'Menjahit', 'fesyen', 'M-0005', 20, null, self::PERTAMINA],
            ['bkl', 2023, 'Handycraft', 'kriya', null, 20, null, self::NO_DATA],
            ['bkl', 2023, 'Garnish', 'kuliner', null, 20, null, self::NO_DATA],
            ['bkl', 2024, 'Tata Boga (Cake Ulang Tahun)', 'kuliner', null, 20, null, self::NO_DATA],
            ['bkl', 2024, 'Jahit / Handicraft', 'fesyen', null, 10, null, self::NO_DATA],
            ['bkl', 2024, 'Make Up', 'kecantikan', null, 10, null, self::NO_DATA],
            ['bkl', 2024, 'Batik', 'kriya', null, 20, null, self::NO_DATA],
            ['bkl', 2024, 'Sabun Lerak', 'lainnya', null, 10, null, self::NO_DATA],
            ['bkl', 2024, 'Pertanian', 'lainnya', null, 20, null, self::NO_DATA],
            ['bkl', 2025, 'Tata Boga (Bakery)', 'kuliner', null, 20, 20, null],
            ['bkl', 2025, 'Hair Stylist & Make Up Artist', 'kecantikan', null, 20, 20, null],
            ['bkl', 2025, 'Membatik', 'kriya', null, 20, 20, null],
            ['bkl', 2026, 'Membuat Buket', 'kriya', null, 15, null, self::NO_DATA],

            ['ygy', 2023, 'Memasak', 'kuliner', 'M-0013', 10, 10, null],
            ['ygy', 2023, 'Membatik dan Shibori', 'fesyen', 'M-0008', 20, 1, null],
            ['ygy', 2023, 'Jahit', 'fesyen', 'M-0013', 10, 10, null],
            ['ygy', 2023, 'Pertanian dan Perikanan', 'lainnya', 'M-0011', 10, 1, null],
            ['ygy', 2024, 'Pembuatan Olahan Permen Herbal', 'kuliner', 'M-0013', 15, 15, null],
            ['ygy', 2024, 'Ecoprint', 'kriya', 'M-0008', 19, 19, null],
            ['ygy', 2024, 'Kerajinan Aksesoris', 'kriya', 'M-0013', 10, 10, null],
            ['ygy', 2024, 'Menjahit', 'fesyen', 'M-0013', 10, 10, null],
            ['ygy', 2025, 'Pembuatan Tempe', 'kuliner', 'M-0006', 30, 30, null],
            ['ygy', 2025, 'Menjahit', 'fesyen', 'M-0007', 10, 10, null],
            ['ygy', 2025, 'Desain Grafis', 'keterampilan_digital', 'M-0007', 10, 10, null],
            ['ygy', 2025, 'Budidaya Melon', 'lainnya', 'M-0012', 30, 30, null],
            ['ygy', 2025, 'Budidaya Nanas Bagong', 'lainnya', 'M-0014', 32, null, self::DASH],
            ['ygy', 2025, 'Pembuatan Media Tanam dan Pupuk Organik Cair (POC)', 'lainnya', 'M-0007', 30, 30, null],
            ['ygy', 2025, 'Ecoprint', 'kriya', 'M-0008', 10, null, self::DASH],
            ['ygy', 2025, 'Potong Rambut', 'kecantikan', 'M-0009', 20, 20, null],
            ['ygy', 2025, 'Make Up', 'kecantikan', 'M-0009', 20, 20, null],
            ['ygy', 2025, 'Facial', 'kecantikan', 'M-0009', 20, 20, null],
            ['ygy', 2025, 'Hair Stylish', 'kecantikan', 'M-0009', 10, 10, null],
            ['ygy', 2025, 'Creambath', 'kecantikan', 'M-0009', 20, 20, null],
            ['ygy', 2025, 'Manicure Pedicure', 'kecantikan', 'M-0009', 10, 10, null],
            ['ygy', 2025, 'Beauty Class', 'kecantikan', 'M-0010', 24, 24, null],
            ['ygy', 2025, 'Cooking Class', 'kuliner', 'M-0010', 43, 43, null],
        ];

        $uptIds = Upt::whereIn('name', $upts)->pluck('id', 'name');
        $partnerIds = Partner::withTrashed()->pluck('id', 'code');

        DB::transaction(function () use ($rows, $upts, $uptIds, $partnerIds): void {
            foreach ($rows as [$upt, $year, $name, $field, $partnerCode, $participants, $certified, $note]) {
                $isInstructorNote = $note === self::NO_INSTRUCTOR;

                $program = new Program([
                    'upt_id' => $uptIds[$upts[$upt]],
                    'year' => $year,
                    'name' => $name,
                    'competency_field' => $field,
                    'partner_id' => $partnerCode !== null ? $partnerIds[$partnerCode] : null,
                    'instructor_note' => $isInstructorNote ? $note : null,
                    'total_participants' => $participants,
                    'total_certified' => $certified,
                    'notes' => $isInstructorNote ? null : $note,
                ]);

                $program->certification_status = CertificationStatus::fromCount($certified);
                $program->save();
            }
        });
    }
}
