<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\Note;
use Doctrine\ORM\EntityManager;
use JustSteveKing\StatusCode\Http;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Response\TextResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class GetMessageBodyHandler implements RequestHandlerInterface
{
    public function __construct(private readonly EntityManager $entityManager)
    {}

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        $noteId = $request->getAttribute('note');
        $repository = $this->entityManager->getRepository(Note::class);
        $note = $repository->find($noteId);

        if (! $note instanceof Note) {
            return new JsonResponse(
                [
                    "message" => "Note not found.",
                    "detail" => "No note with note ID {$noteId} was found.",
                ],
                Http::NOT_FOUND->value
            );
        }

        $response = new TextResponse($note->getDescription(), Http::OK->value);
        return $response
            ->withHeader(
                'content-disposition',
                'attachment; filename=note.txt;'
            )
            ->withHeader(
                'content-type',
                'text/plain; charset=utf-8'
            );
    }
}
