<?php

namespace App\Type;

use DateTimeImmutable;

class PrintableDateTimeImmutable extends DateTimeImmutable
{
    public function __toString()
    {
        return $this->format('U');
    }
}
