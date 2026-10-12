<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const RENAMES = [
        'LPP Kelas IIB Bengkulu' => 'Lapas Perempuan Kelas IIB Bengkulu',
        'LPP Kelas IIB Yogyakarta' => 'Lapas Perempuan Kelas IIB Yogyakarta',
    ];

    public function up(): void
    {
        foreach (self::RENAMES as $from => $to) {
            $this->rename($from, $to);
        }
    }

    public function down(): void
    {
        foreach (self::RENAMES as $from => $to) {
            $this->rename($to, $from);
        }
    }

    private function rename(string $from, string $to): void
    {
        $upt = DB::table('upts')->where('name', $from)->first();

        if ($upt === null || DB::table('upts')->where('name', $to)->exists()) {
            return;
        }

        $aliases = collect(json_decode($upt->aliases ?? '[]', true) ?: [])
            ->reject(fn (string $alias): bool => $alias === $to)
            ->push($from)
            ->unique()
            ->values()
            ->all();

        DB::table('upts')->where('id', $upt->id)->update([
            'name' => $to,
            'aliases' => json_encode($aliases),
            'updated_at' => now(),
        ]);
    }
};
