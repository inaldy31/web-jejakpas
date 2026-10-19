<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    private const ACCOUNTS = [
        'lppbandung' => 'Lapas Perempuan Kelas IIA Bandung',
        'lppyogyakarta' => 'Lapas Perempuan Kelas IIB Yogyakarta',
        'lppbengkulu' => 'Lapas Perempuan Kelas IIB Bengkulu',
    ];

    public function up(): void
    {
        $upts = DB::table('upts')->get(['id', 'name', 'aliases']);

        foreach (self::ACCOUNTS as $username => $uptName) {
            $user = DB::table('users')->where('username', $username)->first(['id', 'upt_id', 'is_primary_admin']);

            if ($user === null || $user->upt_id !== null || $user->is_primary_admin) {
                continue;
            }

            $matches = $upts->filter(fn (object $upt): bool => collect([$upt->name, ...(json_decode($upt->aliases ?? '[]', true) ?: [])])
                ->contains(fn (mixed $name): bool => is_string($name) && $this->normalize($name) === $this->normalize($uptName)));

            if ($matches->count() !== 1) {
                echo PHP_EOL."  PERINGATAN: akun {$username} tidak ditautkan ke UPT \"{$uptName}\" (UPT itu tidak ditemukan lewat nama atau alias, atau ditemukan lebih dari satu), jadi akun ini tetap Admin pusat. Pilih UPT-nya di Kelola Admin.".PHP_EOL;

                continue;
            }

            DB::table('users')->where('id', $user->id)->whereNull('upt_id')->update(['upt_id' => $matches->first()->id]);
        }
    }

    public function down(): void {}

    private function normalize(string $name): string
    {
        return Str::of($name)->lower()->replace('.', '')->squish()->toString();
    }
};
