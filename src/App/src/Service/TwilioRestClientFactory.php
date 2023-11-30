<?php

declare(strict_types=1);

namespace App\Service;

use Psr\Container\ContainerInterface;
use Twilio\Rest\Client;

class TwilioRestClientFactory
{
    public function __invoke(ContainerInterface $container): Client
    {
        $config = $container->get('config');

        return new Client(
            $config['twilio']['account_sid'],
            $config['twilio']['auth_token']
        );
    }
}