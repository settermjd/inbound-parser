<?php

namespace App\Service;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class MonologServiceFactory
{
    public function __invoke(ContainerInterface $container): LoggerInterface
    {
        $config = $container->get('config');
        $logConfig = $config['logger'];

        $logger = new Logger($logConfig['name']);
        foreach ($logConfig['handlers'] as $handlerName => $handlerConfig) {
            if ($handlerName === 'stream') {
                $logger->pushHandler(new StreamHandler(
                    $handlerConfig['name'],
                    $handlerConfig['level']
                ));
            }
            if ($handlerName === 'file') {
                $logger->pushHandler(new StreamHandler(
                    $handlerConfig['name'],
                    $handlerConfig['level']
                ));
            }
        }

        return $logger;
    }
}