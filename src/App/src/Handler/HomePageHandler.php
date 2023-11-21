<?php

declare(strict_types=1);

namespace App\Handler;

use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Mail\Message;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;

class HomePageHandler implements RequestHandlerInterface
{
    public function __construct(
        public readonly LoggerInterface $logger
    ) {}

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $parsedBody = $request->getParsedBody();
        /*$emailMessage = Message::fromString($parsedBody['email']);
        $envelope = json_decode($parsedBody['envelope'], true);
        $context = [
            'to' => $parsedBody['to'],
            'from' => $envelope['from'],
            'subject' => $parsedBody['subject'],
            'spam_score' => $parsedBody['spam_score'],
            'envelope' => $parsedBody['envelope'],
            'email' => [
                'parts' => [],
            ]
        ];*/

        /**
        $parts = $emailMessage->getBody()->getParts();
        foreach ($parts as $part) {
            $context['attachment'][] = [
                'Content-Type' => $part->getType(),
                'Name' => $part->getFileName(),
                'Size' => strlen($part->getContent()),
            ];
        }
         */

        $this->logger
            ->debug(
                'Raw inbound parsed data from SendGrid',
                [$parsedBody['email']]
            );

        return new JsonResponse('');
    }
}
