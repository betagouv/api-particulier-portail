<?php

namespace App\Security;

use Symfony\Component\Mime\Encoder\EncoderInterface;

class ApiKeyEncoder implements EncoderInterface
{
    public function encodeString(string $string, ?string $charset = 'utf-8', int $firstLineOffset = 0, int $maxLineLength = 0): string
    {
        return hash("sha512", $string);
    }
}
