<?php

namespace App\Controller;

use Exception;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

class AuthApiController
{
    /**
     * @var SessionInterface
     */
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @Route("/connect/auth-api", name="connect_auth-api_start")
     */
    public function connectAction(ClientRegistry $clientRegistry, Request $request)
    {
        $referer = $request->headers->get('referer');

        $this->session->set('original_target', $referer);
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

    /**
     * @Route("/connect/auth-api/logout", name="connect_auth-api_logout")
     */
    public function logoutAction()
    {
        // controller can be blank: it will never be executed!
        throw new Exception('Don\'t forget to activate logout in security.yaml');
    }
}
