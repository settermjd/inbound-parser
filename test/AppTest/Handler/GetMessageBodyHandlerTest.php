<?php

declare(strict_types=1);

namespace AppTest\Handler;

use App\Entity\Note;
use App\Handler\GetMessageBodyHandler;
use App\Repository\NoteRepository;
use Doctrine\ORM\EntityManager;
use JustSteveKing\StatusCode\Http;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Response\TextResponse;
use Laminas\Mail\Message;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

class GetMessageBodyHandlerTest extends TestCase
{
    public function testReturns404WhenMatchingNoteIsNotFound()
    {
        $noteId = 12;
        $noteRepository = $this->createMock(NoteRepository::class);
        $noteRepository
            ->expects($this->once())
            ->method('find')
            ->with($noteId)
            ->willReturn(null);

        $entityManager = $this->createMock(EntityManager::class);
        $entityManager
            ->expects($this->once())
            ->method('getRepository')
            ->with(Note::class)
            ->willReturn($noteRepository);

        $handler = new GetMessageBodyHandler($entityManager);
        $request = $this->createMock(ServerRequestInterface::class);
        $request
            ->expects($this->once())
            ->method('getAttribute')
            ->with('note')
            ->willReturn(12);

        $response = $handler->handle($request);
        $this->assertSame(
            Http::NOT_FOUND->value,
            $response->getStatusCode()
        );
        $this->assertInstanceOf(JsonResponse::class, $response);

        $responseBody = file_get_contents(__DIR__ . '/../../_files/note-not-found-response-body.json');
        $this->assertSame($responseBody, $response->getBody()->getContents());
    }

    public function testRequestReturnsTextFileWithPlainTextBodyOfMessage()
    {
        $noteDescription = "This is a test email with 1 attachment.";
        $note = $this->createMock(Note::class);
        $note
            ->expects($this->once())
            ->method('getDescription')
            ->willReturn($noteDescription);

        $noteId = 12;
        $noteRepository = $this->createMock(NoteRepository::class);
        $noteRepository
            ->expects($this->once())
            ->method('find')
            ->with($noteId)
            ->willReturn($note);

        $entityManager = $this->createMock(EntityManager::class);
        $entityManager
            ->expects($this->once())
            ->method('getRepository')
            ->with(Note::class)
            ->willReturn($noteRepository);

        $handler = new GetMessageBodyHandler($entityManager);
        $request = $this->createMock(ServerRequestInterface::class);
        $request
            ->expects($this->once())
            ->method('getAttribute')
            ->with('note')
            ->willReturn(12);

        $response = $handler->handle($request);
        $this->assertSame(
            Http::OK->value,
            $response->getStatusCode()
        );
        $this->assertEquals("attachment; filename=note.txt;", $response->getHeaderLine('content-disposition'));
        $this->assertEquals("text/plain; charset=utf-8", $response->getHeaderLine('content-type'));

        $this->assertInstanceOf(TextResponse::class, $response);
        $this->assertSame(
            $noteDescription,
            $response->getBody()->getContents()
        );
    }
}
