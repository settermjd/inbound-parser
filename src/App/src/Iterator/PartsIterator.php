<?php

declare(strict_types=1);

namespace App\Iterator;

use Laminas\Mime\Part;

class PartsIterator extends \RecursiveArrayIterator
{
    public function hasChildren(): bool
    {
        /** @var Part $part */
        $part = $this->current();
        return ! empty($part->getParts());
    }

    public function getChildren(): \RecursiveArrayIterator
    {
        /** @var Part $current */
        $current = $this->current();
        return new PartsIterator($current->getParts());
    }
}