<?php

namespace AppTest\Service;

use App\Service\EmailParserService;
use PHPUnit\Framework\TestCase;

class EmailParserServiceTest extends TestCase
{
    public static function subjectLineProvider(): array
    {
        return [
            [
                "Reference ID: 123AQPOIU98765",
                true,
            ],
            [
                "reference id: 123AQPOIU98765",
                true,
            ],
            [
                "ref id: 123AQPOIU98765",
                true,
            ],
            [
                "referenceid: 123AQPOIU98765",
                false,
            ],
            [
                "referenced: 123AQPOIU98765",
                false,
            ],
            [
                "ref: 123AQPOIU98765",
                false,
            ],
            [
                "Reference: 123AQPOIU98765",
                false,
            ],
            [
                "reference: 123AQPOIU98765",
                false,
            ],
            [
                "id: 123AQPOIU98765",
                false,
            ],
            [
                "ID: 123AQPOIU98765",
                false,
            ],
            [
                "",
                false,
            ],
            [
                "Reference ID: 123AQPOIU9865",
                false,
            ],
            [
                "Reference ID: 123AQPOIU98-765",
                false,
            ],
            [
                "Reference ID: 123AQPOIU98$765",
                false,
            ],
        ];
    }

    /**
     * @dataProvider subjectLineProvider
     */
    public function testCanValidateEmailSubject(
        string $subjectLine,
        bool $matches,
    )
    {
        $service = new EmailParserService();
        $this->assertSame(
            $service->isValidSubjectLine($subjectLine),
            $matches
        );
    }

    public function testCanGetReferenceIdFromSubject()
    {
        $subjectLine = "Reference ID: 123AQPOIU98765";
        $service = new EmailParserService();
        $this->assertSame("123AQPOIU98765", $service->getReferenceId($subjectLine));
    }
}
