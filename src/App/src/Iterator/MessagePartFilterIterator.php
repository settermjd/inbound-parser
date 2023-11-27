<?php

declare(strict_types=1);

namespace App\Iterator;

use Laminas\Mime\Part;

class MessagePartFilterIterator extends \RecursiveFilterIterator
{
    public function accept(): bool
    {
        if ($this->hasChildren()) {
            return true;
        }

        /** @var Part $part */
        $part = $this->current();
        return str_starts_with($part->getType(), "text/plain")
            || str_starts_with($part->getType(), "text/html");
    }

    public function hasChildren(): bool
    {
        return $this->getInnerIterator()->hasChildren();
    }

    public function getChildren(): MessagePartFilterIterator
    {
        return new self($this->getInnerIterator()->getChildren());
    }
}