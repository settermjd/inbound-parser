<?php

declare(strict_types=1);

namespace AppTest\Iterator;

use App\Iterator\MessagePartFilterIterator;
use App\Iterator\PartsIterator;
use Laminas\Mail\Message;
use Laminas\Mime\Part;
use PHPUnit\Framework\TestCase;

class PartsIteratorTest extends TestCase
{
    public function testIteratesSuccessfullyOverPartsData()
    {
        $email = file_get_contents(
            __DIR__ . '/../../_files/mail_with_pdf_attachment.eml'
        );
        $message = Message::fromString($email);

        /** @var Part[] $iterator */
        $iterator = new \RecursiveIteratorIterator(
            new PartsIterator(
                $message->getBody()->getParts(),
            ),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        $this->assertCount(4, $iterator);
    }
}
