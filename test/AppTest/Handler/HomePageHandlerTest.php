<?php

declare(strict_types=1);

namespace AppTest\Handler;

use App\Handler\HomePageHandler;
use Laminas\Diactoros\Response\JsonResponse;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;

class HomePageHandlerTest extends TestCase
{
    protected MockObject|ContainerInterface $container;

    protected function setUp(): void
    {
        $this->container = $this->createMock(
            ContainerInterface::class
        );
    }

    public function testReturnsJsonResponse(): void
    {
        $homePage = new HomePageHandler();
        $response = $homePage->handle(
            $this->createMock(
                ServerRequestInterface::class
            )
        );

        self::assertInstanceOf(
            JsonResponse::class,
            $response
        );
        self::assertSame('""', $response->getBody()->getContents());
    }
}
