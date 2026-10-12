<?php

namespace App\Http\Controllers\Public;

use App\Enums\KnowledgeCategory;
use App\Models\KnowledgeItem;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class KnowledgeController
{
    public function index(): View
    {
        $items = KnowledgeItem::query()->published()->orderBy('title')->get()->groupBy(fn (KnowledgeItem $item) => $item->category->value);

        return view('public.knowledge.index', [
            'sections' => collect(KnowledgeCategory::cases())
                ->map(fn (KnowledgeCategory $category): array => ['category' => $category, 'items' => $items->get($category->value, collect())])
                ->filter(fn (array $section): bool => $section['items']->isNotEmpty()),
        ]);
    }

    public function download(KnowledgeItem $knowledgeItem): StreamedResponse
    {
        abort_unless($knowledgeItem->isPublished(), 404);

        return $knowledgeItem->privateFileDownload();
    }
}
