<?php

namespace App\Filament\Resources\KnowledgeItems\Pages;

use App\Filament\Resources\KnowledgeItems\KnowledgeItemResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditKnowledgeItem extends EditRecord
{
    protected static string $resource = KnowledgeItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            KnowledgeItemResource::downloadAction(),
            DeleteAction::make(),
            RestoreAction::make(),
            ForceDeleteAction::make(),
        ];
    }
}
