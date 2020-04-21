<?php

namespace App\DoctrineType;

use App\Type\PrintableDateTimeImmutable;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\DateTimeTzImmutableType;

class IdDateTimeType extends DateTimeTzImmutableType
{
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        $dateTime = parent::convertToPHPValue($value, $platform);

        if (!$dateTime) {
            return $dateTime;
        }

        return new PrintableDateTimeImmutable('@' . $dateTime->format('U'));
    }

    public function getName()
    {
        return 'iddatetime';
    }
}
