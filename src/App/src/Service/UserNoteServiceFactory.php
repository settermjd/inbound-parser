<?php

namespace App\Service;

use Doctrine\ORM\EntityManager;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class UserNoteServiceFactory
{
    public function __invoke(ContainerInterface $container): UserNoteService
    {
        return new UserNoteService(
            $container->get(EntityManager::class),
            $container->get(LoggerInterface::class),
        );
    }
}