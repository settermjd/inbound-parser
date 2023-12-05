<?php

declare(strict_types=1);

namespace AppTest\Handler;

use App\Handler\HomePageHandler;
use App\Handler\HomePageHandlerFactory;
use App\Service\EmailParserService;
use App\Service\TwilioService;
use App\Service\UserNoteService;
use Doctrine\ORM\EntityManager;
use Laminas\EventManager\EventManager;
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
        $this->container
            ->expects($this->atMost(5))
            ->method('get')
            ->willReturnOnConsecutiveCalls(
                $this->createMock(EntityManager::class),
                $this->createMock(EmailParserService::class),
                $this->createMock(UserNoteService::class),
                $this->createMock(TwilioService::class),
                $this->createMock(EventManager::class)
            );

        $factory  = new HomePageHandlerFactory();
        $homePage = $factory($this->container);

        self::assertInstanceOf(HomePageHandler::class, $homePage);
    }
}
