<?php

namespace App\Support;

use App\Models\Partner;
use App\Models\Program;
use App\Models\Upt;
use Closure;

final class FilterOptions
{
    public static function years(int|string|null $selected): array
    {
        $years = Program::query()->published()->distinct()->orderByDesc('year')->pluck('year')
            ->map(fn ($year): int => (int) $year)
            ->all();

        $options = self::keepSelected(array_combine($years, $years), $selected, fn (int|string $year): string => (string) $year);
        krsort($options);

        return $options;
    }

    public static function keepSelected(array $options, int|string|null $selected, Closure $label): array
    {
        if (filled($selected) && ! array_key_exists($selected, $options)) {
            $options[$selected] = $label($selected);
        }

        return $options;
    }

    public static function uptLabel(int|string $id): string
    {
        $upt = Upt::find($id);

        return $upt?->isNamePublic() ? $upt->name : __('jejak.public.filter.other_upt');
    }

    public static function partnerLabel(int|string $id): string
    {
        return Partner::query()->published()->find($id)?->name ?? __('jejak.public.filter.partner_unavailable');
    }
}
