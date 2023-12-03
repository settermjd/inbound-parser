<?php

declare(strict_types=1);

namespace App\Service;

use Psr\Container\ContainerInterface;
use Twilio\Rest\Client;

class TwilioServiceFactory
{
    public function __invoke(ContainerInterface $container): TwilioService
    {
        $client = $container->get(Client::class);
        $config = $container->get('config');
        $sender = $config['twilio']['phone_number'];
        $baseUrl = $config['app']['baseUrl'];

        return new TwilioService($client, $sender, $baseUrl);
    }
}