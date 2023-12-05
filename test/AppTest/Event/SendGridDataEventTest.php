<?php

declare(strict_types=1);

namespace AppTest\Event;

use App\Event\SendGridDataEvent;
use Laminas\EventManager\Event;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class SendGridDataEventTest extends TestCase
{
    public function testLogsEventDataWhenCalled()
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger
            ->expects($this->once())
            ->method('info');

        $obj = new SendGridDataEvent($logger);
        $obj($this->createMock(Event::class));
    }
}
