<?php

declare(strict_types=1);

namespace App\Handler;

use App\Service\EmailParserService;
use App\Service\TwilioService;
use App\Service\UserNoteService;
use Doctrine\ORM\EntityManager;
use Laminas\EventManager\Event;
use Laminas\EventManager\EventManager;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;

class HomePageHandlerFactory
{
    public function __invoke(ContainerInterface $container): HomePageHandler
    {
        return new HomePageHandler(
            $container->get(EntityManager::class),
            $container->get(EmailParserService::class),
            $container->get(UserNoteService::class),
            $container->get(TwilioService::class),
            $container->get(EventManager::class),
        );
    }
}
