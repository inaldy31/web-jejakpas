<?php

namespace App\Http\Controllers\Admin;

use App\Models\KnowledgeItem;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class KnowledgeItemDownloadController
{
    public function __invoke(KnowledgeItem $knowledgeItem): StreamedResponse
    {
        $response = $knowledgeItem->privateFileDownload();

        activity('files')->performedOn($knowledgeItem)->event('downloaded')->log('downloaded');

        return $response;
    }
}
