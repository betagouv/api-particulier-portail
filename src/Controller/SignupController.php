<?php

namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SignupController
{
    /**
     * @Route("/connect/signup", name="connect_signup_start")
     */
    public function connectAction(ClientRegistry $clientRegistry)
    {
        return $clientRegistry->getClient('signup')->redirect([], []);
    }

    /**
     * @Route("/connect/signup/check", name="connect_signup_check")
     */
    public function connectCheckAction(Request $request, ClientRegistry $clientRegistry)
    {
        $client = $clientRegistry->getClient('signup');

        try {
            $user = $client->fetchUser();
            return new JsonResponse($user->toArray());
        } catch (IdentityProviderException $e) {
            var_dump($e->getMessage());
        }
    }
}
