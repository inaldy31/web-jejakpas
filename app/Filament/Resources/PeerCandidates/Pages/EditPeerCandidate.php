<?php

namespace App\Filament\Resources\PeerCandidates\Pages;

use App\Filament\Resources\PeerCandidates\PeerCandidateResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditPeerCandidate extends EditRecord
{
    protected static string $resource = PeerCandidateResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make(), RestoreAction::make(), ForceDeleteAction::make()];
    }
}
