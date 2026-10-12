<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;
use Filament\Support\Contracts\HasLabel;

enum PartnerGroup: string implements HasLabel
{
    use HasOptions;

    case LapasUpt = 'lapas_upt';
    case LembagaPendidikan = 'lembaga_pendidikan';
    case PerguruanTinggi = 'perguruan_tinggi';
    case OrganisasiProfesi = 'organisasi_profesi';
    case DuniaUsaha = 'dunia_usaha';
    case Bumn = 'bumn';
    case NgoKomunitas = 'ngo_komunitas';
    case Media = 'media';

    public function getLabel(): string
    {
        return match ($this) {
            self::LapasUpt => 'Lapas/UPT',
            self::LembagaPendidikan => 'Lembaga Pendidikan/Vokasi',
            self::PerguruanTinggi => 'Perguruan Tinggi',
            self::OrganisasiProfesi => 'Organisasi Profesi',
            self::DuniaUsaha => 'Dunia Usaha/UMKM',
            self::Bumn => 'BUMN',
            self::NgoKomunitas => 'NGO/Komunitas',
            self::Media => 'Media',
        };
    }
}
