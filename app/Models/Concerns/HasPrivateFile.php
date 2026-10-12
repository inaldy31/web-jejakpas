<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

trait HasPrivateFile
{
    protected static function bootHasPrivateFile(): void
    {
        static::forceDeleted(function (Model $model): void {
            DB::afterCommit(fn () => Storage::disk('local')->delete($model->file_path));
        });

        static::updated(function (Model $model): void {
            if (! $model->wasChanged('file_path')) {
                return;
            }

            $oldPath = $model->getOriginal('file_path');

            DB::afterCommit(fn () => Storage::disk('local')->delete($oldPath));
        });
    }

    public function downloadName(): string
    {
        $slug = Str::slug($this->title);
        $baseName = $slug === '' ? 'dokumen' : $slug;

        $extension = pathinfo($this->file_path, PATHINFO_EXTENSION);

        return $extension === '' ? $baseName : $baseName.'.'.$extension;
    }

    public function privateFileDownload(): StreamedResponse
    {
        abort_unless(Storage::disk('local')->exists($this->file_path), 404);

        return Storage::disk('local')->download($this->file_path, $this->downloadName(), ['X-Content-Type-Options' => 'nosniff']);
    }
}
