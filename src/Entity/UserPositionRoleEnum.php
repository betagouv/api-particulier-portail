<?php

namespace App\Entity;

abstract class UserPositionRoleEnum
{
    const TYPE_FUNCTIONAL = "functional";
    const TYPE_TECHNICAL  = "technical";

    protected static $typeName = [
        self::TYPE_FUNCTIONAL => "Fonctionnel",
        self::TYPE_TECHNICAL  => "Technique"
    ];

    public static function getTypeName($typeShortName)
    {
        if (!isset(self::$typeName[$typeShortName])) {
            return "Unknown type ($typeShortName)";
        }

        return static::$typeName[$typeShortName];
    }

    public static function getAvailableTypes()
    {
        return [
            self::TYPE_FUNCTIONAL,
            self::TYPE_TECHNICAL
        ];
    }
}
