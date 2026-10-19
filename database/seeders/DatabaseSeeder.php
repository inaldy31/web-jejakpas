<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            UptAdminSeeder::class,
            UptSeeder::class,
            PartnerSeeder::class,
            ProgramSeeder::class,
            BaselineSnapshotSeeder::class,
        ]);
    }
}
