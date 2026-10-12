<?php

namespace App\Http\Controllers\Public;

use App\Domain\Impact\ImpactStats;
use App\Models\Upt;
use Illuminate\Support\Arr;
use Illuminate\View\View;

final class HomeController
{
    public function __invoke(): View
    {
        return view('public.home', [
            'summary' => Arr::except((new ImpactStats)->summary(), 'ready'),
            'pilotUpts' => Upt::where('is_pilot', true)->orderBy('short_name')->get(),
        ]);
    }
}
