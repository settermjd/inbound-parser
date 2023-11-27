<?php

declare(strict_types=1);

namespace AppTest\Iterator;

use App\Iterator\MessagePartFilterIterator;
use App\Iterator\PartsIterator;
use Laminas\Mail\Message;
use Laminas\Mime\Part;
use PHPUnit\Framework\TestCase;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;

class MessagePartFilterIteratorTest extends TestCase
{
    public function testIteratesSuccessfullyOverPartsData()
    {
        $email = file_get_contents(
            __DIR__ . '/../../_files/mail_with_pdf_attachment.eml'
        );
        $message = Message::fromString($email);

        /** @var Part[] $iterator */
        $iterator = new RecursiveIteratorIterator(
            new MessagePartFilterIterator(
                new PartsIterator(
                    $message->getBody()->getParts()
                ),
            )
        );

        $this->assertCount(2, $iterator);
    }
}
