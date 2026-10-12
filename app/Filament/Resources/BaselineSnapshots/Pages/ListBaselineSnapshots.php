<?php

namespace App\Filament\Resources\BaselineSnapshots\Pages;

use App\Filament\Resources\BaselineSnapshots\BaselineSnapshotResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBaselineSnapshots extends ListRecords
{
    protected static string $resource = BaselineSnapshotResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
