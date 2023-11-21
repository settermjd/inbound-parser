<?php

namespace AppTest\Service;

use App\Service\MonologServiceFactory;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class MonologServiceFactoryTest extends TestCase
{
    public function testCanReturnLoggerInterfaceWhenAvailable()
    {
        $factory = new MonologServiceFactory();
        $container = $this->createMock(ContainerInterface::class);
        $container
            ->expects($this->once())
            ->method('get')
            ->with('config')
            ->willReturn([
                'logger' => [
                    'name' => 'logger',
                    'handlers' => [
                        'stream' => [
                            'name' => 'php://stdout',
                            'level' => Level::Debug
                        ]
                    ]
                ]
            ]);
        /** @var LoggerInterface|Logger $logger */
        $logger = $factory($container);

        $this->assertInstanceOf(LoggerInterface::class, $logger);
        $this->assertSame('logger', $logger->getName());
        $this->assertCount(1, $logger->getHandlers());

        /** @var StreamHandler $handler */
        $handler = $logger->getHandlers()[0];
        $this->assertInstanceOf(StreamHandler::class, $handler);
        $this->assertSame(Level::Debug, $handler->getLevel());

    }
}
