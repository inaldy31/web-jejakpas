<?php

namespace App\Http\Requests;

use App\Enums\CompetencyField;
use App\Enums\PartnerGroup;
use App\Enums\PksStatus;
use App\Models\Upt;
use Illuminate\Validation\Rule;

final class PartnerFilterRequest extends PublicFilterRequest
{
    private const MAX_REGION_LENGTH = 255;

    public function rules(): array
    {
        return [
            'kelompok' => ['bail', 'nullable', Rule::enum(PartnerGroup::class)],
            'status_pks' => ['bail', 'nullable', Rule::enum(PksStatus::class)],
            'bidang' => ['bail', 'nullable', Rule::enum(CompetencyField::class)],
            'upt' => ['bail', 'nullable', 'integer', Rule::exists(Upt::class, 'id')],
            'wilayah' => ['bail', 'nullable', 'string', 'max:'.self::MAX_REGION_LENGTH],
            ...self::pageRules(),
        ];
    }
}
