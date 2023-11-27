<?php

declare(strict_types=1);

namespace App\Handler;

use App\Service\EmailParserService;
use App\Service\UserService;
use Doctrine\ORM\EntityManager;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;

class HomePageHandlerFactory
{
    public function __invoke(ContainerInterface $container): RequestHandlerInterface
    {
        return new HomePageHandler(
            $container->get(EntityManager::class),
            $container->get(EmailParserService::class),
            $container->get(UserService::class),
            $container->get(LoggerInterface::class)
        );
    }
}
