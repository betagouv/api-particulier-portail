<?php

namespace App\OAuth2Server\Entity;

use League\OAuth2\Client\Token\AccessTokenInterface;
use League\OAuth2\Server\Entities\Traits\AccessTokenTrait;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\Traits\TokenEntityTrait;

class AccessToken implements AccessTokenInterface
{
    use AccessTokenTrait, EntityTrait, TokenEntityTrait;

    public function __construct(string $userIdentifier, array $scopes = [])
    {
        $this->setUserIdentifier($userIdentifier);
        foreach ($scopes as $scope) {
            $this->addScope($scope);
        }
    }
}
