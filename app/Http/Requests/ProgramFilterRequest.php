<?php

namespace App\Http\Requests;

use App\Enums\CompetencyField;
use App\Models\Upt;
use Illuminate\Validation\Rule;

final class ProgramFilterRequest extends ImpactFilterRequest
{
    public function rules(): array
    {
        return [
            ...parent::rules(),
            'upt' => ['bail', 'nullable', 'integer', Rule::exists(Upt::class, 'id')],
            'bidang' => ['bail', 'nullable', Rule::enum(CompetencyField::class)],
            'mitra' => ['bail', 'nullable', 'integer', 'min:1'],
            ...self::pageRules(),
        ];
    }
}
