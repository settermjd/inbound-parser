<?php

namespace Service;

use App\Service\UserNoteService;
use App\Service\UserNoteServiceFactory;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class UserNoteServiceFactoryTest extends TestCase
{
    public function testCanInstantiateUserNoteService()
    {
        $container = $this->createMock(ContainerInterface::class);
        $container
            ->expects($this->atMost(2))
            ->method('get')
            ->willReturnOnConsecutiveCalls(
                $this->createMock(EntityManager::class),
                $this->createMock(LoggerInterface::class),
            );
        $this->assertInstanceOf(
            UserNoteService::class,
            (new UserNoteServiceFactory())($container)
        );
    }
}
