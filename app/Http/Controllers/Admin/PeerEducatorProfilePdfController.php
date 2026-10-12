<?php

namespace App\Http\Controllers\Admin;

use App\Enums\CandidateStatus;
use App\Models\PeerCandidate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

final class PeerEducatorProfilePdfController
{
    public function __invoke(PeerCandidate $peerCandidate): Response
    {
        abort_unless($peerCandidate->current_status === CandidateStatus::PeerEducatorAktif, 404);

        $peerCandidate->load([
            'upt',
            'assessments' => fn ($query) => $query->orderByDesc('assessed_at')->orderByDesc('id'),
        ]);

        activity('peer_educator')->performedOn($peerCandidate)->event('profile_printed')->log('profile_printed');

        return Pdf::loadView('pdf.peer-educator-profile', [
            'candidate' => $peerCandidate,
            'latest' => $peerCandidate->latestFinalAssessment,
        ])->stream("profil-{$peerCandidate->code}.pdf");
    }
}
