<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

abstract class PublicFilterRequest extends FormRequest
{
    private const INTEGER_FIELDS = ['tahun', 'upt', 'mitra', 'page'];

    private const MAX_PAGE = 10000;

    public function filters(): array
    {
        return Arr::whereNotNull(Arr::except($this->validated(), ['page']));
    }

    public function page(): int
    {
        return (int) ($this->validated('page') ?? 1);
    }

    public function redirectBeyondLastPage(LengthAwarePaginator $paginator): ?RedirectResponse
    {
        if ($paginator->currentPage() <= $paginator->lastPage()) {
            return null;
        }

        $this->session()->reflash();

        return redirect()->route($this->route()->getName(), Arr::whereNotNull([
            ...$this->filters(),
            'page' => $paginator->lastPage() > 1 ? $paginator->lastPage() : null,
        ]));
    }

    public function attributes(): array
    {
        return [
            'tahun' => __('jejak.public.filter.attributes.tahun'),
            'upt' => __('jejak.public.filter.attributes.upt'),
            'bidang' => __('jejak.public.filter.attributes.bidang'),
            'mitra' => __('jejak.public.filter.attributes.mitra'),
            'kelompok' => __('jejak.public.filter.attributes.kelompok'),
            'status_pks' => __('jejak.public.filter.attributes.status_pks'),
            'wilayah' => __('jejak.public.filter.attributes.wilayah'),
            'page' => __('jejak.public.filter.attributes.page'),
        ];
    }

    protected static function pageRules(): array
    {
        return ['page' => ['bail', 'nullable', 'integer', 'min:1', 'max:'.self::MAX_PAGE]];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(collect(self::INTEGER_FIELDS)
            ->mapWithKeys(fn (string $field): array => [$field => $this->input($field)])
            ->filter(fn (mixed $value): bool => is_string($value))
            ->map(fn (string $value): string => self::canonicalInteger($value))
            ->all());
    }

    protected function getRedirectUrl(): string
    {
        $failed = array_map(fn (string $key): string => Str::before($key, '.'), $this->validator->errors()->keys());
        $valid = Arr::only($this->query(), array_diff(array_keys($this->rules()), $failed));

        return $this->redirector->getUrlGenerator()->route($this->route()->getName(), Arr::whereNotNull($valid)).'#filter';
    }

    private static function canonicalInteger(string $value): string
    {
        if (preg_match('/^([+-]?)(\d+)$/', trim($value), $parts) !== 1) {
            return $value;
        }

        $digits = ltrim($parts[2], '0');

        return match (true) {
            $digits === '' => '0',
            $parts[1] === '-' => '-'.$digits,
            default => $digits,
        };
    }
}
