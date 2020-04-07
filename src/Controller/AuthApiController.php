<?php

namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class AuthApiController
{
    /**
     * @Route("/connect/auth-api", name="connect_auth-api_start")
     */
    public function connectAction(ClientRegistry $clientRegistry)
    {
        return $clientRegistry->getClient('auth-api')->redirect([], []);
    }

    /**
     * @Route("/connect/auth-api/check", name="connect_auth-api_check")
     */
    public function connectCheckAction(ClientRegistry $clientRegistry)
    {
        $client = $clientRegistry->getClient('auth-api');

        try {
            $user = $client->fetchUser();
            return new JsonResponse($user->toArray());
        } catch (IdentityProviderException $e) {
            throw $e;
        }
    }
}