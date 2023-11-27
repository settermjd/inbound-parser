<?php

declare(strict_types=1);

namespace AppTest\Handler;

use App\Handler\HomePageHandler;
use App\Handler\HomePageHandlerFactory;
use App\Service\EmailParserService;
use App\Service\UserService;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class HomePageHandlerFactoryTest extends TestCase
{
    protected MockObject|ContainerInterface $container;

    protected function setUp(): void
    {
        $this->container = $this->createMock(ContainerInterface::class);
    }

    public function testFactoryWithoutTemplate(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $entityManager = $this->createMock(EntityManager::class);

        $this->container
            ->expects($this->atMost(4))
            ->method('get')
            ->willReturnOnConsecutiveCalls(
                $entityManager,
                $this->createMock(EmailParserService::class),
                $this->createMock(UserService::class),
                $logger
            );

        $factory  = new HomePageHandlerFactory();
        $homePage = $factory($this->container);

        self::assertInstanceOf(HomePageHandler::class, $homePage);
    }
}
