<?php

declare(strict_types=1);

namespace App\Service;

use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Api\V2010\Account\MessageInstance;
use Twilio\Rest\Client;

class TwilioService implements SmsServiceInterface
{
    public function __construct(
        private readonly Client $client
    ) {
    }

    /**
     * @return MessageInstance
     * @throws TwilioException
     */
    public function sendSMS(string $message, string $recipient, string $sender)
    {
        return $this->client
            ->messages
            ->create($recipient,
                [
                    "body" => $message,
                    "sender" => $sender
                ]
            );
    }
}