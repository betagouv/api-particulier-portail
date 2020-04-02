<?php

namespace App\OAuth2Server\Repository;

use App\OAuth2Server\Entity\Scope;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;

class ScopeRepository implements ScopeRepositoryInterface
{
    public function getScopeEntityByIdentifier($identifier): ScopeEntityInterface
    {
        if (Scope::hasScope($identifier)) {
            return new Scope($identifier);
        }
    }

    public function finalizeScopes(array $scopes, $grantType, ClientEntityInterface $clientEntity, $userIdentifier = null): array
    {
        $filteredScopes = [];
        /** @var Scope $scope */
        foreach ($scopes as $scope) {
            $hasScope = Scope::hasScope($scope->getIdentifier());
            if ($hasScope) {
                $filteredScopes[] = $scope;
            }
        }
        return $filteredScopes;
    }
}
