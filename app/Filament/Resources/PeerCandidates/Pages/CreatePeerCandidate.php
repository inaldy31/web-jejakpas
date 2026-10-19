<?php

namespace App\Filament\Resources\PeerCandidates\Pages;

use App\Filament\Resources\PeerCandidates\PeerCandidateResource;
use App\Support\UptAccess;
use Filament\Resources\Pages\CreateRecord;

class CreatePeerCandidate extends CreateRecord
{
    protected static string $resource = PeerCandidateResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return UptAccess::forceUpt($data);
    }
}
