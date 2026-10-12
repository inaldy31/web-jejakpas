<?php

namespace App\Domain\Partners;

final class ImportResult
{
    public int $created = 0;

    public int $updated = 0;

    public int $publishedUpdated = 0;

    public array $errors = [];

    public function summary(): string
    {
        $summary = __('jejak.partners.import.summary', [
            'created' => $this->created,
            'updated' => $this->updated,
            'errors' => count($this->errors),
        ]);

        if ($this->publishedUpdated > 0) {
            $summary .= ' '.__('jejak.partners.import.published_updated', ['count' => $this->publishedUpdated]);
        }

        return $summary;
    }
}
