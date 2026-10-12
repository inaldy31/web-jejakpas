<?php

namespace App\Filament\Resources\PeerCandidates\Pages;

use App\Filament\Resources\PeerCandidates\PeerCandidateResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPeerCandidates extends ListRecords
{
    protected static string $resource = PeerCandidateResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
