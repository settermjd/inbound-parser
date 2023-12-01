<?php

declare(strict_types=1);

namespace App\Handler;

use Doctrine\ORM\EntityManager;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

class GetMessageBodyHandlerFactory
{
    public function __invoke(ContainerInterface $container) : GetMessageBodyHandler
    {
        return new GetMessageBodyHandler($container->get(EntityManager::class));
    }
}
