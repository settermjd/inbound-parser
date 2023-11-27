<?php

namespace App\Service;

class EmailParserService
{
    public const IS_VALID_SUBJECT = 1;

    /**
     * This regular expreession checks that the subject line matches one of the following patterns (case-insensitive):
     *
     * - Reference ID: <Reference ID>
     * - Ref ID: <Reference ID>
     *
     * "<Reference ID>" is a 14 character string that can contain lower and uppercase letters, as well as any digit between 0 and 9 (inclusive).
     */
    public const VALID_SUBJECT_REGEX = "/^(?i:Ref(erence)? ID: )(?<refid>[0-9a-zA-Z]{14})$/";

    public function isValidSubjectLine(string $subjectLine): bool
    {
        if ($subjectLine === '') {
            return false;
        }

        $result = preg_match(self::VALID_SUBJECT_REGEX, $subjectLine);
        return $result === self::IS_VALID_SUBJECT;
    }
}