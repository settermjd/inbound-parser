<?php

declare(strict_types=1);

namespace App;

use Laminas\EventManager\EventManager;
use Laminas\EventManager\EventManagerInterface;

trait EventManagerAwareTrait
{
    private ?EventManagerInterface $events = null;

    public function hasEventManager(): bool
    {
        return $this->events instanceof EventManagerInterface;
    }

    public function setEventManager(EventManagerInterface $events) : self
    {
        $events->setIdentifiers([
            __CLASS__,
            get_called_class(),
        ]);
        $this->events = $events;
        return $this;
    }

    public function getEventManager(): EventManagerInterface
    {
        if (null === $this->events) {
            $this->setEventManager(new EventManager());
        }
        return $this->events;
    }
}