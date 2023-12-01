<?php

declare(strict_types=1);

namespace AppTest\Handler;

use App\Handler\GetMessageBodyHandler;
use App\Handler\GetMessageBodyHandlerFactory;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class GetMessageBodyHandlerFactoryTest extends TestCase
{
    public function testCanInstantiateHandlerCorrectly()
    {
        $container = $this->createMock(ContainerInterface::class);
        $container
            ->expects($this->once())
            ->method('get')
            ->with(EntityManager::class)
            ->willReturn($this->createMock(EntityManager::class));

        $this->assertInstanceOf(
            GetMessageBodyHandler::class,
            (new GetMessageBodyHandlerFactory())(
                $container
            )
        );
    }
}
