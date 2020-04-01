<?php

namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ApiAuthController
{
    /**
     * @Route("/connect/api-auth", name="connect_api-auth_start")
     */
    public function connectAction(ClientRegistry $clientRegistry)
    {
        return $clientRegistry->getClient('api-auth')->redirect([], []);
    }

    /**
     * @Route("/connect/api-auth/check", name="connect_api-auth_check")
     */
    public function connectCheckAction(ClientRegistry $clientRegistry)
    {
        $client = $clientRegistry->getClient('api-auth');

        try {
            $user = $client->fetchUser();
            return new JsonResponse($user->toArray());
        } catch (IdentityProviderException $e) {
            var_dump($e->getMessage());
        }
    }
}
