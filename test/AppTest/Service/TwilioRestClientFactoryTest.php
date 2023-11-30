<?php

declare(strict_types=1);

namespace AppTest\Service;

use App\Service\TwilioRestClientFactory;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Twilio\Rest\Client;

class TwilioRestClientFactoryTest extends TestCase
{
    public function testCanInstantiateTwilioServiceInstanceProperly()
    {
        $container = $this->createMock(ContainerInterface::class);
        $container
            ->expects($this->once())
            ->method('get')
            ->willReturn(
                [
                    'twilio' => [
                        'account_sid' => '1234567890',
                        'auth_token' => '1234567890',
                    ]
                ]
            );

        $factory = new TwilioRestClientFactory();

        $this->assertInstanceOf(Client::class, $factory($container));
    }
}
