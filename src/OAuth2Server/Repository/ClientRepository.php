<?php

namespace App\OAuth2Server\Repository;

use App\OAuth2Server\Entity\Client;
use App\Repository\ClientRepository as AppClientRepository;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;

class ClientRepository implements ClientRepositoryInterface
{
    /**
     * @var AppClientRepository
     */
    private $clientRepository;

    public function __construct(AppClientRepository $clientRepository)
    {
        $this->clientRepository = $clientRepository;
    }

    public function getClientEntity(
        $clientIdentifier
    ) {
        $appClient = $this->clientRepository->findOneBy([
            'active' => true,
            'id' => $clientIdentifier
        ]);
        if ($appClient === null) {
            return null;
        }
        $oauthClient = new Client($clientIdentifier, $appClient->getName(), $appClient->getRedirect());
        return $oauthClient;
    }

    public function validateClient($clientIdentifier, $clientSecret, $grantType)
    {
        $appClient = $this->clientRepository->findOneBy([
            'active' => true,
            'id' => $clientIdentifier
        ]);
        if ($appClient === null) {
            return null;
        }

        if (!hash_equals($appClient->getSecret(), (string) $clientSecret)) {
            return null;
        }

        $oauthClient = new Client($clientIdentifier, $appClient->getName(), $appClient->getRedirect());
        return $oauthClient;
    }
}
