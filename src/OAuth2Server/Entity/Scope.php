<?php

namespace App\OAuth2Server\Entity;

use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Entities\Traits\EntityTrait;

class Scope implements ScopeEntityInterface
{
    use EntityTrait;

    public static $scopes = [];

    public function __construct(string $name)
    {
        $this->setIdentifier($name);
    }

    public static function hasScope($id): bool
    {
        return $id === '*' || array_key_exists($id, static::$scopes);
    }

    public function jsonSerialize()
    {
        return $this->getIdentifier();
    }
}
