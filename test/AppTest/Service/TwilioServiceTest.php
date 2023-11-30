<?php

namespace AppTest\Service;

use App\Entity\User;
use App\Service\TwilioService;
use Laminas\Mime\Part;
use PHPUnit\Framework\TestCase;
use Twilio\Rest\Api\V2010\Account\MessageInstance;
use Twilio\Rest\Api\V2010\Account\MessageList;
use Twilio\Rest\Client;

class TwilioServiceTest extends TestCase
{
    public function testCanSendConfirmationSMS()
    {
        $message = "Hi %s. This a quick confirmation that \"%s\" have been added as a note on your account, along with the text, which you can find in the attachment to this SMS.";
        $recipient = new User(
            "Dave Grohl",
            "dave@foofighters.example.com",
            "+6140912341234"
        );
        $sender = "+6140912341244";

        $messageInstance = $this->createMock(MessageInstance::class);
        $messageInstance->status = 'queued';

        $client = $this->createMock(Client::class);
        $client->messages = $this->createMock(MessageList::class);
        $client->messages
            ->expects($this->once())
            ->method('create')
            ->with(
                $recipient->getPhoneNumber(),
                [
                    'body' => sprintf(
                        $message,
                        $recipient->getName(),
                        "test document.pdf"
                    ),
                    'sender' => $sender,
                ]
            )
            ->willReturn(
                $messageInstance
            );

        $twilioService = new TwilioService($client, $sender);

        $attachment = $this->createMock(Part::class);
        $attachment
            ->expects($this->once())
            ->method('getFileName')
            ->willReturn('test document.pdf');

        $this->assertTrue(
            $twilioService->sendNewNoteNotification(
                recipient: $recipient,
                attachments: [
                    $attachment
                ]
            )
        );
    }
}
