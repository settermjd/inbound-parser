<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\User;
use App\Service\EmailParserService;
use App\Service\TwilioService;
use App\Service\UserNoteService;
use Doctrine\ORM\EntityManager;
use JustSteveKing\StatusCode\Http;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Mail\Message;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;

class HomePageHandler implements RequestHandlerInterface
{
    public function __construct(
        public readonly EntityManager      $entityManager,
        public readonly EmailParserService $emailParserService,
        public readonly UserNoteService    $userService,
        private readonly TwilioService     $twilioService,
        public readonly LoggerInterface    $logger
    ) {}

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $parsedBody = $request->getParsedBody();

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

        $this->logger
            ->info(
                'Inbound parsed data from SendGrid',
                [
                    'subject' => $parsedBody['subject'],
                    'spam_score' => $parsedBody['spam_score'],
                    'envelope' => $parsedBody['envelope'],
                    'email' => $parsedBody['email'],
                ]
            );

        $emailMessage = Message::fromString($parsedBody['email']);

        $this->userService->createNote($emailMessage);

        $referenceId = $this->emailParserService
            ->getReferenceId($parsedBody['subject']);
        $senderEmail = $emailMessage
            ->getFrom()
            ->current()
            ->getEmail();

        $attachments = array_filter(
            $emailMessage->getBody()->getParts(),
            function ($part, $index) {
                return str_starts_with((string)$part->getDisposition(), "attachment");
            },
            ARRAY_FILTER_USE_BOTH
        );
        $pdfFilename = $attachments[1]->getDisposition();

        $userRepository = $this->entityManager->getRepository(User::class);
        $user = $userRepository->findOneBy([
            'email' => $emailMessage->getFrom()->current()->getEmail()
        ]);

        $result = $this->twilioService
            ->sendNewNoteNotification(
                recipient: $user,
                attachments: $attachments
            );

        return new JsonResponse(
            [
                'status' => 'success',
                'data' => [
                    'reference id' => $referenceId,
                    'from' => $senderEmail,
                ]
            ],
            Http::CREATED->value
        );
    }

}
