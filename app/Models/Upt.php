<?php

namespace App\Models;

use App\Enums\UptType;
use App\Models\Concerns\RecordsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Upt extends Model
{
    use HasFactory, RecordsActivity;

    protected $fillable = ['name', 'short_name', 'aliases', 'type', 'province', 'is_pilot'];

    protected function casts(): array
    {
        return [
            'aliases' => 'array',
            'type' => UptType::class,
            'is_pilot' => 'boolean',
        ];
    }

    public static function normalizeName(?string $value): string
    {
        return Str::of((string) $value)->lower()->replace('.', '')->squish()->toString();
    }

    public static function findByNameOrAlias(string $name): ?self
    {
        $needle = self::normalizeName($name);

        return static::query()->get()->first(
            fn (self $upt): bool => collect([$upt->name, ...($upt->aliases ?? [])])
                ->contains(fn (string $candidate): bool => self::normalizeName($candidate) === $needle)
        );
    }

    public function isNamePublic(): bool
    {
        return $this->is_pilot
            || $this->partners()->published()->exists()
            || $this->programs()->published()->exists();
    }

    public function partners(): BelongsToMany
    {
        return $this->belongsToMany(Partner::class);
    }

    public function programs(): HasMany
    {
        return $this->hasMany(Program::class);
    }

    public function candidates(): HasMany
    {
        return $this->hasMany(PeerCandidate::class);
    }

    public function snapshots(): HasMany
    {
        return $this->hasMany(BaselineSnapshot::class);
    }
}
