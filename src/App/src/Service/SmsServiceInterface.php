<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Note;

interface SmsServiceInterface
{
    public function sendNewNoteNotification(
        Note $note,
        array $attachments = [],
    ): bool;
}