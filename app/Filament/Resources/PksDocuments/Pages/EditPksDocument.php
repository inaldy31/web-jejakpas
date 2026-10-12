<?php

namespace App\Filament\Resources\PksDocuments\Pages;

use App\Filament\Resources\PksDocuments\PksDocumentResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditPksDocument extends EditRecord
{
    protected static string $resource = PksDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            PksDocumentResource::downloadAction(),
            DeleteAction::make(),
            RestoreAction::make(),
            ForceDeleteAction::make(),
        ];
    }
}
