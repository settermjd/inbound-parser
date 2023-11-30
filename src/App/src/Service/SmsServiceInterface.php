<?php

namespace App\Service;

use App\Entity\User;

interface SmsServiceInterface
{
    public function sendNewNoteNotification(
        User $recipient,
        array $attachments = [],
    ): bool;
}