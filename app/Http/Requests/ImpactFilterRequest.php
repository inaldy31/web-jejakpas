<?php

namespace App\Http\Requests;

use App\Models\Upt;
use Illuminate\Validation\Rule;

class ImpactFilterRequest extends PublicFilterRequest
{
    public function rules(): array
    {
        return [
            'tahun' => ['bail', 'nullable', 'integer', 'between:2000,2100'],
            'upt' => ['bail', 'nullable', 'integer', Rule::exists(Upt::class, 'id')->where('is_pilot', true)],
        ];
    }
}
