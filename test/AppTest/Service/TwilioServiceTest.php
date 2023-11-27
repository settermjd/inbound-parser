<?php

namespace AppTest\Service;

use App\Service\TwilioService;
use PHPUnit\Framework\TestCase;
use Twilio\Rest\Api\V2010\Account\MessageInstance;
use Twilio\Rest\Api\V2010\Account\MessageList;
use Twilio\Rest\Client;

class TwilioServiceTest extends TestCase
{
    public function testCanSendConfirmationSMS()
    {
        $message = "Here is the message";
        $recipient = "+6140912341234";
        $sender = "+6140912341244";

        $client = $this->createMock(Client::class);
        $client->messages = $this->createMock(MessageList::class);
        $client->messages
            ->expects($this->once())
            ->method('create')
            ->with(
                $recipient,
                [
                    'body' => $message,
                    'sender' => $sender,
                ]
            )
            ->willReturn(
                $this->createMock(MessageInstance::class)
            );

        $twilioService = new TwilioService($client);

        $this->assertInstanceOf(
            MessageInstance::class,
            $twilioService->sendSMS(
                message: $message,
                recipient: $recipient,
                sender: $sender,
            )
        );
    }
}
