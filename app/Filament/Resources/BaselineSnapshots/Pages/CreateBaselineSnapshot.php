<?php

namespace App\Filament\Resources\BaselineSnapshots\Pages;

use App\Filament\Resources\BaselineSnapshots\BaselineSnapshotResource;
use App\Support\UptAccess;
use Filament\Resources\Pages\CreateRecord;

class CreateBaselineSnapshot extends CreateRecord
{
    protected static string $resource = BaselineSnapshotResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return UptAccess::forceUpt($data);
    }
}
