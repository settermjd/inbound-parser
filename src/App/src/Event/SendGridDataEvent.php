<?php

declare(strict_types=1);

namespace App\Event;

use Laminas\EventManager\Event;
use Psr\Log\LoggerInterface;

class SendGridDataEvent
{
    public function __construct(private readonly LoggerInterface $logger)
    {

    }

    public function __invoke(Event $event): void
    {
        $this->logger->info(
            'Inbound parsed data from SendGrid',
            [
                'event' => $event->getName(),
                'params' => $event->getParams(),
            ]
        );
    }
}