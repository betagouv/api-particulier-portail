<?php

namespace App\OAuth2Server\Repository;

use App\Entity\AccessToken as AppAccessToken;
use App\OAuth2Server\Entity\AccessToken;
use App\Repository\AccessTokenRepository as AppAccessTokenRepository;
use Doctrine\ORM\EntityManagerInterface;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;

class AccessTokenRepository implements AccessTokenRepositoryInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    public function getNewToken(ClientEntityInterface $clientEntity, array $scopes, $userIdentifier = null)
    {
        return new AccessToken($userIdentifier, $scopes);
    }

    public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity)
    {
        $appAccessToken = new AppAccessToken(
            $accessTokenEntity->getIdentifier(),
            $accessTokenEntity->getUserIdentifier(),
            $accessTokenEntity->getClient()->getIdentifier(),
            $this->scopesToArray($accessTokenEntity->getScopes()),
            false,
            $accessTokenEntity->getExpiryDateTime()
        );
        $this->entityManager->persist($appAccessToken);
        $this->entityManager->flush();
    }

    public function revokeAccessToken($tokenId)
    {
        $appAccessToken = $this->accessTokenRepository->find($tokenId);
        if ($appAccessToken === null) {
            return;
        }
        $appAccessToken->setRevoked(true);

        $this->entityManager->persist($appAccessToken);
        $this->entityManager->flush();
    }

    public function isAccessTokenRevoked($tokenId)
    {
        $appAccessToken = $this->accessTokenRepository->find($tokenId);
        if ($appAccessToken === null) {
            return true;
        }
        return $appAccessToken->getRevoked();
    }

    private function scopesToArray(array $scopes): array
    {
        return array_map(function ($scope) {
            return $scope->getIdentifier();
        }, $scopes);
    }
}
