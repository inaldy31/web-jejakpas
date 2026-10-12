<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;
use Filament\Support\Contracts\HasLabel;

enum KnowledgeCategory: string implements HasLabel
{
    use HasOptions;

    case SopPksPedoman = 'sop_pks_pedoman';
    case ModulPelatihan = 'modul_pelatihan';
    case PanduanAsesmen = 'panduan_asesmen';
    case ToolkitImplementasi = 'toolkit_implementasi';
    case PublikasiDokumentasi = 'publikasi_dokumentasi';

    public function getLabel(): string
    {
        return match ($this) {
            self::SopPksPedoman => 'SOP, PKS & Pedoman',
            self::ModulPelatihan => 'Modul Pelatihan',
            self::PanduanAsesmen => 'Panduan Asesmen',
            self::ToolkitImplementasi => 'Toolkit Implementasi',
            self::PublikasiDokumentasi => 'Publikasi & Dokumentasi',
        };
    }
}
