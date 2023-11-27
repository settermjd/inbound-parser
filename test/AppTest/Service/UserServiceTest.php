<?php

namespace AppTest\Service;

use App\Entity\Attachment;
use App\Entity\Note;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\UserService;
use Doctrine\ORM\EntityManager;
use Laminas\Mail\Message;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class UserServiceTest extends TestCase
{
    private EntityManager|MockObject $entityManager;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManager::class);
    }

    public function testCanCreateNoteForExistingUser()
    {
        $email = file_get_contents(
            __DIR__ . '/../../_files/mail_with_pdf_attachment.eml'
        );
        $logger = $this->createMock(LoggerInterface::class);
        $user = $this->createMock(User::class);
        $note = new Note(
            details: 'This is a test email with 1 attachment.',
            user: $user,
        );
        $pdf = file_get_contents(__DIR__ . '/../../_files/test.pdf');
        $attachment = new Attachment(
            file: $pdf,
            note: $note
        );

        $userRepository = $this->createMock(UserRepository::class);
        $userEmail = "example@example.com";
        $userRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['email' => $userEmail])
            ->willReturn($user);

        $this->entityManager
            ->expects($this->once())
            ->method('getRepository')
            ->willReturnOnConsecutiveCalls($userRepository);

        $this->entityManager
            ->expects($this->atMost(2))
            ->method('persist');

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $emailMessage = Message::fromString($email);

        $service = new UserService($this->entityManager, $logger);
        $result = $service->createNote($emailMessage);

        $this->assertTrue($result);
    }
}
