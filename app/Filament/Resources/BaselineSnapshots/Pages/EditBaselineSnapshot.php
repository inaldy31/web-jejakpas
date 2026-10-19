<?php

namespace App\Filament\Resources\BaselineSnapshots\Pages;

use App\Filament\Resources\BaselineSnapshots\BaselineSnapshotResource;
use App\Support\UptAccess;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditBaselineSnapshot extends EditRecord
{
    protected static string $resource = BaselineSnapshotResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return UptAccess::forceUpt($data);
    }

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make(), RestoreAction::make(), ForceDeleteAction::make()];
    }
}
