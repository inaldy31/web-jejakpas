<?php

namespace App\Models\Exceptions;

use RuntimeException;

final class PublicationBlocked extends RuntimeException
{
    public function __construct(public readonly array $blockers)
    {
        parent::__construct(implode(', ', $blockers));
    }
}
