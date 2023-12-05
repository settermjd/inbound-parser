<?php

declare(strict_types=1);

namespace AppTest\Handler;

use App\Event\SendGridDataEvent;
use App\Handler\HomePageHandler;
use App\Handler\HomePageHandlerLoggerListenerFactory;
use Laminas\EventManager\Event;
use Laminas\EventManager\EventManager;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class HomePageHandlerLoggerListenerFactoryTest extends TestCase
{
    public function testCanAttachTheSendGridDataEventWhenInstantiatingAHomePageHandler()
    {
        $logger = $this->createMock(LoggerInterface::class);

        $eventManager = $this->createMock(EventManager::class);
        $eventManager
            ->expects($this->once())
            ->method('attach')
            ->with(
                'sendgrid_data',
                new SendGridDataEvent($logger)
            );

        $container = $this->createMock(ContainerInterface::class);
        $container
            ->expects($this->atMost(2))
            ->method('get')
            ->willReturnOnConsecutiveCalls(
                $logger,
                $eventManager,
            );

        $callback = new class($this->createMock(HomePageHandler::class)) {
            public function __construct(private readonly HomePageHandler $handler)
            {}

            public function __invoke()
            {
                return $this->handler;
            }
        };

        $factory = new HomePageHandlerLoggerListenerFactory();
        $this->assertInstanceOf(
            HomePageHandler::class,
            $factory(
                container: $container,
                name: '',
                callback: $callback
            )
        );
    }
}
