<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use Laminas\Mime\Part;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Api\V2010\Account\MessageInstance;
use Twilio\Rest\Client;

class TwilioService implements SmsServiceInterface
{
    private string $templateMessageWithAttachment = <<<EOF
Hi %s. This a quick confirmation that "%s" has been added as a note on your account, along with the text, which you can find in the attachment to this SMS.
EOF;

    private string $templateMessageWithAttachments = <<<EOF
Hi %s. This a quick confirmation that "%s" have been added as a note on your account, along with the text, which you can find in the attachment to this SMS.
EOF;

    public function __construct(
        private readonly Client $client,
        private readonly string $sender
    ) {
    }

    /**
     * @param Part[] $attachments
     * @throws TwilioException
     */
    public function sendNewNoteNotification(
        User $recipient,
        array $attachments = [],
    ): bool
    {
        $bodyTemplate = $this->templateMessageWithAttachments;
        $message = $this->client->messages
            ->create($recipient->getPhoneNumber(),
                [
                    "body" => sprintf(
                        $bodyTemplate,
                        $recipient->getName(),
                        implode(
                            ', ',
                            array_map(
                                fn(Part $value): string => $value->getFilename() ?? '',
                                $attachments
                            )
                        )
                    ),
                    "sender" => $this->sender
                ]
            );

        return in_array(
            $message->status,
            [
                'accepted',
                'delivered',
                'queued',
                'read',
                'received',
                'receiving',
                'scheduled',
                'sending',
                'sent',
                'undelivered',
            ]
        );
    }
}