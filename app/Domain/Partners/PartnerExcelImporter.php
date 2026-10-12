<?php

namespace App\Domain\Partners;

use App\Enums\CompetencyField;
use App\Enums\PartnerGroup;
use App\Enums\PksStatus;
use App\Enums\SupportType;
use App\Enums\SustainabilityStatus;
use App\Models\Partner;
use App\Models\Upt;
use App\Support\FormLimits;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Exception as SpreadsheetException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Throwable;

final class PartnerExcelImporter
{
    public const SHEET = 'Data Mitra';

    private const COLUMNS = [
        'ID Mitra' => 'code',
        'Nama Mitra' => 'name',
        'Kelompok Mitra' => 'group',
        'Wilayah/Provinsi' => 'province',
        'Jabatan Kontak' => 'contact_position',
        'Kontak (No./Email)' => 'contact',
        'Nomor PKS' => 'pks_number',
        'Tanggal PKS' => 'pks_date',
        'Masa Berlaku s.d.' => 'pks_valid_until',
        'Ruang Lingkup Kerja Sama' => 'scope',
        'Status PKS' => 'pks_status',
        'Bidang Kompetensi' => 'competency_fields',
        'Jenis Dukungan' => 'support_types',
        'UPT Mitra' => 'upts',
        'Jml Pelatihan' => 'total_trainings',
        'Jml Peserta' => 'total_participants',
        'Jml Bersertifikat' => 'total_certified',
        'Jml Jadi Peer Educator' => 'total_peer_educators',
        'Jml Penerima Manfaat Lanjutan' => 'total_beneficiaries',
        'Skor Rata-rata Confidence Q.' => 'avg_confidence_score',
        'Status Keberlanjutan' => 'sustainability_status',
        'Ringkasan Praktik Baik' => 'best_practice_summary',
        'Narahubung (Jabatan)' => 'liaison_position',
        'Update Terakhir' => null,
    ];

    private const VARCHAR_FIELDS = ['province', 'contact_position', 'contact', 'pks_number', 'liaison_position'];

    private const TEXT_FIELDS = ['scope', 'best_practice_summary'];

    private const INTEGER_FIELDS = ['total_trainings', 'total_participants', 'total_certified', 'total_peer_educators', 'total_beneficiaries'];

    private const MAX_VARCHAR_LENGTH = 255;

    private const DATE_FORMATS = ['Y-m-d', 'd/m/Y', 'd-m-Y', 'j/n/Y', 'j-n-Y'];

    private const MIN_DATE = '1990-01-01';

    private const MAX_DATE = '2100-12-31';

    private const FORMULA_ERRORS = ['#NULL!', '#DIV/0!', '#VALUE!', '#REF!', '#NAME?', '#NUM!', '#N/A', '#CALC!', '#SPILL!', '#GETTING_DATA'];

    private ?array $uptIdsByNormalizedName = null;

    public function import(string $path): ImportResult
    {
        $reader = IOFactory::createReader(IOFactory::READER_XLSX);

        try {
            $worksheetInfo = collect($reader->listWorksheetInfo($path));

            $info = $worksheetInfo->firstWhere('worksheetName', self::SHEET)
                ?? throw new ImportException(__('jejak.partners.import.sheet_not_found', ['sheet' => self::SHEET]));

            $maxRows = (int) config('jejak.import.max_rows');
            $maxColumns = (int) config('jejak.import.max_columns');

            if ($info['totalRows'] > $maxRows + 1) {
                throw new ImportException(__('jejak.partners.import.too_many_rows', ['max' => $maxRows]));
            }

            if ($info['totalColumns'] > $maxColumns) {
                throw new ImportException(__('jejak.partners.import.too_many_columns', ['max' => $maxColumns]));
            }

            $reader->setReadDataOnly(true)->setLoadSheetsOnly([self::SHEET]);
            $spreadsheet = $reader->load($path);
        } catch (SpreadsheetException $e) {
            throw new ImportException(__('jejak.partners.import.unreadable_file'), previous: $e);
        }

        $sheet = $spreadsheet->getSheetByName(self::SHEET);

        $rows = $sheet->toArray(null, true, false, false);
        $columns = $this->mapHeader(array_shift($rows) ?? []);
        $result = new ImportResult;

        $parsedRows = [];
        $firstRowForCode = [];

        foreach ($rows as $index => $row) {
            $values = [];
            $rowNumber = $index + 2;

            foreach ($columns as $position => $field) {
                $values[$field] = $this->clean($row[$position] ?? null);
            }

            if (array_key_exists('code', $values)) {
                $values['code'] = $this->canonicalizeCode($values['code']);
            }

            if (($values['name'] ?? null) === null) {
                $hasOtherData = collect($values)->except(['code', 'name'])->filter(fn (mixed $value): bool => $value !== null)->isNotEmpty();

                if ($hasOtherData) {
                    $result->errors[$rowNumber] = __('jejak.partners.import.name_required');
                }

                continue;
            }

            $formulaErrorField = collect($values)->search(fn (mixed $value): bool => is_string($value) && in_array($value, self::FORMULA_ERRORS, true));

            if ($formulaErrorField !== false) {
                $result->errors[$rowNumber] = __('jejak.partners.import.formula_error', [
                    'label' => $this->label($formulaErrorField),
                    'value' => $values[$formulaErrorField],
                ]);

                continue;
            }

            $code = $values['code'] ?? null;

            if ($code !== null) {
                if (isset($firstRowForCode[$code])) {
                    $result->errors[$rowNumber] = __('jejak.partners.import.duplicate_code', [
                        'code' => $code,
                        'row' => $firstRowForCode[$code],
                    ]);

                    continue;
                }

                $firstRowForCode[$code] = $rowNumber;
            }

            $parsedRows[$rowNumber] = $values;
        }

        $withCode = array_filter($parsedRows, fn (array $values): bool => ($values['code'] ?? null) !== null);
        $withoutCode = array_filter($parsedRows, fn (array $values): bool => ($values['code'] ?? null) === null);

        $existingPartnersByCode = collect($withCode)->pluck('code')->unique()->values()
            ->chunk(900)
            ->flatMap(fn (Collection $chunk) => Partner::withTrashed()->whereIn('code', $chunk)->get())
            ->keyBy('code');

        foreach ($withCode + $withoutCode as $rowNumber => $values) {
            try {
                DB::transaction(fn () => $this->save($values, $result, $existingPartnersByCode));
            } catch (ImportException $e) {
                $result->errors[$rowNumber] = $e->getMessage();
            }
        }

        return $result;
    }

    private function mapHeader(array $header): array
    {
        $normalized = [];

        foreach (self::COLUMNS as $label => $field) {
            $normalized[$this->normalizeHeaderLabel($label)] = $field;
        }

        $columns = [];

        foreach ($header as $position => $label) {
            $field = $normalized[$this->normalizeHeaderLabel((string) $label)] ?? null;

            if ($field !== null) {
                $columns[$position] = $field;
            }
        }

        if (! in_array('name', $columns, true)) {
            throw new ImportException(__('jejak.partners.import.name_column_missing'));
        }

        return $columns;
    }

    private function normalizeSpace(string $value): string
    {
        return trim(preg_replace('/\s+/u', ' ', str_replace("\u{00A0}", ' ', $value)));
    }

    private function normalizeName(string $name): string
    {
        return mb_strtolower($this->normalizeSpace($name));
    }

    private function normalizeHeaderLabel(string $label): string
    {
        return $this->normalizeName(str_replace('●', '', $label));
    }

    private function save(array $values, ImportResult $result, Collection $existingPartnersByCode): void
    {
        $code = $this->code($values['code'] ?? null);

        $attributes = [
            'name' => $this->checkLength((string) $values['name'], 'name'),
            'group' => $this->enum(PartnerGroup::class, $values['group'] ?? null, 'group'),
            'pks_status' => $this->enum(PksStatus::class, $values['pks_status'] ?? null, 'pks_status'),
            'sustainability_status' => $this->enum(SustainabilityStatus::class, $values['sustainability_status'] ?? null, 'sustainability_status'),
            'competency_fields' => $this->enumList(CompetencyField::class, $values['competency_fields'] ?? null, 'competency_fields'),
            'support_types' => $this->enumList(SupportType::class, $values['support_types'] ?? null, 'support_types'),
            'pks_date' => $this->date($values['pks_date'] ?? null, 'pks_date'),
            'pks_valid_until' => $this->date($values['pks_valid_until'] ?? null, 'pks_valid_until'),
            'avg_confidence_score' => $this->score($values['avg_confidence_score'] ?? null),
        ];

        if ($attributes['pks_date'] !== null && $attributes['pks_valid_until'] !== null
            && $attributes['pks_valid_until'] < $attributes['pks_date']) {
            throw new ImportException(__('jejak.partners.import.invalid_validity'));
        }

        foreach (self::VARCHAR_FIELDS as $field) {
            $attributes[$field] = isset($values[$field]) ? $this->checkLength((string) $values[$field], $field) : null;
        }

        foreach (self::TEXT_FIELDS as $field) {
            $attributes[$field] = isset($values[$field]) ? $this->checkLength((string) $values[$field], $field, FormLimits::TEXTAREA) : null;
        }

        foreach (self::INTEGER_FIELDS as $field) {
            $attributes[$field] = $this->integer($values[$field] ?? null, $field);
        }

        $uptIds = $this->uptIds($values['upts'] ?? null);
        $partner = $code !== null ? $existingPartnersByCode->get($code) : null;

        if ($partner !== null) {
            if ($partner->trashed()) {
                throw new ImportException(__('jejak.partners.import.soft_deleted', ['code' => $code]));
            }

            if ($this->normalizeName($partner->name) !== $this->normalizeName($attributes['name'])) {
                throw new ImportException(__('jejak.partners.import.code_name_mismatch', [
                    'code' => $code,
                    'existing' => $partner->name,
                ]));
            }

            $wasPublished = $partner->isPublished();

            $partner->update(array_filter($attributes, static fn (mixed $value): bool => $value !== null));
            $result->updated++;

            if ($wasPublished) {
                $result->publishedUpdated++;
            }
        } else {
            $partner = Partner::create([...$attributes, 'code' => $code]);
            $result->created++;
        }

        if ($uptIds !== []) {
            $partner->syncUpts($uptIds);
        }
    }

    private function canonicalizeCode(mixed $value): mixed
    {
        if (! is_string($value) || ! preg_match('/^M-(\d{4,6})$/', $value, $matches)) {
            return $value;
        }

        return sprintf('M-%04d', (int) $matches[1]);
    }

    private function code(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $code = (string) $value;

        if (! preg_match('/^M-\d{4,6}$/', $code)) {
            throw new ImportException(__('jejak.partners.import.invalid_code', ['value' => $code]));
        }

        return $code;
    }

    private function checkLength(string $text, string $field, int $max = self::MAX_VARCHAR_LENGTH): string
    {
        if (mb_strlen($text) > $max) {
            throw new ImportException(__('jejak.partners.import.text_too_long', [
                'label' => $this->label($field),
                'max' => $max,
                'length' => mb_strlen($text),
            ]));
        }

        return $text;
    }

    private function enum(string $enum, mixed $value, string $field): ?string
    {
        if ($value === null) {
            return null;
        }

        return $enum::fromLabel((string) $value)?->value
            ?? throw new ImportException(__('jejak.partners.import.unknown_value', [
                'label' => $this->label($field),
                'value' => $value,
                'options' => implode(', ', $enum::options()),
            ]));
    }

    private function enumList(string $enum, mixed $value, string $field): ?array
    {
        if ($value === null) {
            return null;
        }

        $parts = array_filter(array_map('trim', preg_split('/[,;]/', (string) $value)));

        if ($parts === []) {
            return null;
        }

        return array_values(array_unique(array_map(fn (string $part) => $this->enum($enum, $part, $field), $parts)));
    }

    private function integer(mixed $value, string $field): ?int
    {
        if ($value === null) {
            return null;
        }

        $invalid = fn () => throw new ImportException(__('jejak.partners.import.invalid_integer', ['label' => $this->label($field), 'value' => $value]));

        if (is_string($value) && ! preg_match('/^\d+$/', $value)) {
            $invalid();
        }

        if (! is_string($value) && ! is_int($value) && ! is_float($value)) {
            $invalid();
        }

        if (! is_string($value) && (! is_finite((float) $value) || $value < 0 || (float) $value !== floor((float) $value))) {
            $invalid();
        }

        if ((float) $value > Partner::MAX_COUNT) {
            $invalid();
        }

        return (int) $value;
    }

    private function score(mixed $value): ?float
    {
        if ($value === null) {
            return null;
        }

        if (! is_numeric($value) || $value < 20 || $value > 100) {
            throw new ImportException(__('jejak.partners.import.invalid_score', ['label' => $this->label('avg_confidence_score'), 'value' => $value]));
        }

        return round((float) $value, 2);
    }

    private function date(mixed $value, string $field): ?string
    {
        if ($value === null) {
            return null;
        }

        if (is_int($value) || is_float($value)) {
            try {
                $date = CarbonImmutable::instance(ExcelDate::excelToDateTimeObject((float) $value));
            } catch (Throwable) {
                throw $this->invalidDate($field, $value);
            }

            return $this->assertDateInRange($date, $field, $value);
        }

        $text = trim((string) $value);

        foreach (self::DATE_FORMATS as $format) {
            try {
                $date = CarbonImmutable::createFromFormat('!'.$format, $text);
            } catch (Throwable) {
                continue;
            }

            if ($date !== false && $date->format($format) === $text) {
                return $this->assertDateInRange($date, $field, $value);
            }
        }

        throw $this->invalidDate($field, $value);
    }

    private function assertDateInRange(CarbonImmutable $date, string $field, mixed $value): string
    {
        $result = $date->toDateString();

        if ($result < self::MIN_DATE || $result > self::MAX_DATE) {
            throw $this->invalidDate($field, $value);
        }

        return $result;
    }

    private function invalidDate(string $field, mixed $value): ImportException
    {
        return new ImportException(__('jejak.partners.import.invalid_date', ['label' => $this->label($field), 'value' => $value]));
    }

    private function uptIds(mixed $value): array
    {
        if ($value === null) {
            return [];
        }

        $names = array_values(array_filter(array_map('trim', preg_split('/[,;]/', (string) $value))));

        return array_map(
            fn (string $name): int => $this->uptIdByName($name)
                ?? throw new ImportException(__('jejak.partners.import.upt_not_found', ['name' => $name])),
            $names,
        );
    }

    private function uptIdByName(string $name): ?int
    {
        $this->uptIdsByNormalizedName ??= Upt::all()->reduce(function (array $map, Upt $upt): array {
            foreach ([$upt->name, ...($upt->aliases ?? [])] as $candidate) {
                $map[Upt::normalizeName($candidate)] ??= $upt->id;
            }

            return $map;
        }, []);

        return $this->uptIdsByNormalizedName[Upt::normalizeName($name)] ?? null;
    }

    private function clean(mixed $value): mixed
    {
        if (is_string($value)) {
            $value = preg_replace('/^\s+|\s+$/u', '', $value);

            return in_array($value, ['', '-', '–', '—'], true) ? null : $value;
        }

        return $value;
    }

    private function label(string $field): string
    {
        return (string) (array_search($field, self::COLUMNS, true) ?: $field);
    }
}
