<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Attachment;
use App\Entity\Note;
use App\Entity\User;
use App\Exception\UserNotFoundException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\NotSupported;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Laminas\Mail\Message;
use Psr\Log\LoggerInterface;

class UserNoteService
{
    public function __construct(
        public readonly EntityManager $entityManager,
        public readonly LoggerInterface $logger
    ){}

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws NotSupported
     * @throws UserNotFoundException
     */
    public function createNote(Message $emailMessage): Note
    {
        $userRepository = $this->entityManager->getRepository(User::class);
        $user = $userRepository->findOneBy([
            'email' => $emailMessage->getFrom()->current()->getEmail()
        ]);
        if ($user === null) {
            $addresses = $emailMessage->getFrom();
            $addresses->rewind();
            $userEmail = $addresses->current()->getEmail();
            $this->logger->debug('Could not retrieve user', [
                'sender' => $userEmail,
            ]);
            throw new UserNotFoundException(
                "User with email address $userEmail was not found."
            );
        }

        $note = new Note(
            details: trim($emailMessage->getPlainTextBodyPart()->getRawContent())
        );
        $note->setUser($user);
        $this->entityManager->persist($note);

        $attachments = $emailMessage->getAttachments();
        foreach ($attachments as $emailAttachment) {
            $attachment = new Attachment(
                file: $emailAttachment->getRawContent()
            );
            $attachment->setNote($note);
            $this->entityManager->persist($attachment);
        }

        $this->entityManager->flush();

        return $note;
    }
}