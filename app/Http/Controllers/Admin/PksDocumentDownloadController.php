<?php

namespace App\Http\Controllers\Admin;

use App\Models\PksDocument;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class PksDocumentDownloadController
{
    public function __invoke(PksDocument $pksDocument): StreamedResponse
    {
        $response = $pksDocument->privateFileDownload();

        activity('files')->performedOn($pksDocument)->event('downloaded')->log('downloaded');

        return $response;
    }
}
