<?php

namespace App\Service;

use App\Entity\Attachment;
use App\Entity\Note;
use App\Entity\User;
use App\Iterator\MessagePartFilterIterator;
use App\Iterator\PartsIterator;
use Doctrine\ORM\EntityManager;
use Laminas\Mail\Message;
use Laminas\Mime\Message as MimeMessage;
use Laminas\Mime\Part;
use Psr\Log\LoggerInterface;
use RecursiveIteratorIterator;

class UserService
{
    public function __construct(
        public readonly EntityManager $entityManager,
        public readonly LoggerInterface $logger
    ){}

    public function createNote(Message $emailMessage): bool
    {
        $userRepository = $this->entityManager->getRepository(User::class);
        $user = $userRepository->findOneBy([
            'email' => $emailMessage->getFrom()->current()->getEmail()
        ]);
        if ($user === null) {
            $addresses = $emailMessage->getFrom();
            $addresses->rewind();
            $sender = $addresses->current()->getEmail();
            $this->logger->debug('Could not retrieve user', [
                'sender' => $sender,
            ]);
        }

        $messageBody = $emailMessage->getBody();

        $attachments = $this->getAttachments($messageBody);
        $noteParts = $this->getEmailMessage($messageBody);
        $content = trim($noteParts[0]->getContent());

        $note = new Note(details: $content,);
        $note->setUser($user);
        $this->entityManager->persist($note);

        foreach ($attachments as $emailAttachment) {
            $attachment = new Attachment(
                file: $emailAttachment->getRawContent()
            );
            $attachment->setNote($note);
            $this->entityManager->persist($attachment);
        }

        $this->entityManager->flush();

        return true;
    }

    /**
     * @param $messageBody
     * @return array
     */
    public function getAttachments($messageBody): array
    {
        return array_filter(
            $messageBody->getParts(),
            function ($part, $index) {
                return str_starts_with((string)$part->getDisposition(), "attachment");
            },
            ARRAY_FILTER_USE_BOTH
        );
    }

    /**
     * @return Part[]
     */
    public function getEmailMessage(MimeMessage $messageBody): array
    {
        $parts = new RecursiveIteratorIterator(
            new MessagePartFilterIterator(
                new PartsIterator($messageBody->getParts())
            ),
            RecursiveIteratorIterator::SELF_FIRST
        );

        return iterator_to_array($parts);
    }
}