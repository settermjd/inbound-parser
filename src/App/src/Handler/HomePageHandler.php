<?php

declare(strict_types=1);

namespace App\Handler;

use App\EventManagerAwareTrait;
use App\Service\EmailParserService;
use App\Service\TwilioService;
use App\Service\UserNoteService;
use Doctrine\ORM\EntityManager;
use JustSteveKing\StatusCode\Http;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\EventManager\EventManager;
use Laminas\Mail\Message;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class HomePageHandler implements RequestHandlerInterface
{
    use EventManagerAwareTrait;

    public function __construct(
        public readonly EntityManager      $entityManager,
        public readonly EmailParserService $emailParserService,
        public readonly UserNoteService    $userNoteService,
        private readonly TwilioService     $twilioService,
        private readonly EventManager      $eventManager
    ) {}

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $parsedBody = $request->getParsedBody();

        if ($this->hasEventManager()) {
            $this->getEventManager()->trigger('sendgrid_data', null, $parsedBody);
        }

        if (
            ! array_key_exists('subject', $parsedBody)
            || ! $this->emailParserService->isValidSubjectLine($parsedBody['subject'])
        ) {
            return new JsonResponse(
                [
                    "message" => "The email subject does not contain a valid reference ID.",
                    "detail" => "Email subject lines must match one of the following two, case-insensitive, formats: 'Reference ID: REF_ID' or 'Ref ID: REF_ID'. REF_ID is a 14 character string. It can contain lower and uppercase letters from A to Z (inclusive), and any digit between 0 and 9 (inclusive).",
                ],
                Http::UNPROCESSABLE_ENTITY->value
            );
        }

        $emailMessage = Message::fromString($parsedBody['email']);

        $this->twilioService
            ->sendNewNoteNotification(
                note: $this->userNoteService->createNote($emailMessage),
                attachments: $emailMessage->getAttachments()
            );

        $senderEmail = $emailMessage
            ->getFrom()
            ->current()
            ->getEmail();

        return new JsonResponse(
            [
                'status' => 'success',
                'data' => [
                    'reference id' => $this->emailParserService->getReferenceId($parsedBody['subject']),
                    'from' => $senderEmail,
                ]
            ],
            Http::CREATED->value
        );
    }

}
