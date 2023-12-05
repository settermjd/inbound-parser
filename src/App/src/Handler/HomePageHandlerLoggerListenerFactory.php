<?php

declare(strict_types=1);

namespace App\Handler;

use App\Event\SendGridDataEvent;
use Laminas\EventManager\EventManager;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class HomePageHandlerLoggerListenerFactory
{
    public function __invoke(ContainerInterface $container, string $name, callable $callback): HomePageHandler
    {
        /** @var LoggerInterface $logger */
        $logger = $container->get(LoggerInterface::class);

        /** @var EventManager $em */
        $em = $container->get(EventManager::class);
        $em->attach(
            'sendgrid_data',
            new SendGridDataEvent($logger)
        );

        $repository = $callback();
        $repository->setEventManager($em);
        return $repository;
    }
}