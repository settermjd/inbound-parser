<?php

namespace AppTest\Service;

use App\Service\TwilioService;
use App\Service\TwilioServiceFactory;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Twilio\Rest\Client;

class TwilioServiceFactoryTest extends TestCase
{
    public function testCanInstantiateTwilioServiceInstanceProperly()
    {
        $container = $this->createMock(ContainerInterface::class);
        $container
            ->expects($this->atMost(2))
            ->method('get')
            ->willReturnOnConsecutiveCalls(
                $this->createMock(Client::class),
                [
                    'app' => [
                        'baseUrl' => 'https://localhost'
                    ],
                    'twilio' => [
                        'phone_number' => '+611234567890'
                    ]
                ]
            );

        $factory = new TwilioServiceFactory();

        $this->assertInstanceOf(TwilioService::class, $factory($container));
    }
}
