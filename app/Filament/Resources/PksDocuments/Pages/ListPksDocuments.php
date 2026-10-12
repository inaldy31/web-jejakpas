<?php

namespace App\Filament\Resources\PksDocuments\Pages;

use App\Filament\Resources\PksDocuments\PksDocumentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPksDocuments extends ListRecords
{
    protected static string $resource = PksDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
