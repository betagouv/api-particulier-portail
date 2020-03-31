<?php

namespace App\Security;

use App\Entity\User;
use App\ApiAuth\ResourceOwner;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class ApiAuthAuthenticator extends SocialAuthenticator
{
    /**
     * @var ClientRegistry
     */
    private $clientRegistry;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(ClientRegistry $clientRegistry, EntityManagerInterface $em, RouterInterface $router)
    {
        $this->clientRegistry = $clientRegistry;
        $this->em = $em;
        $this->router = $router;
    }

    public function supports(Request $request)
    {
        // continue ONLY if the current ROUTE matches the check ROUTE
        return $request->attributes->get('_route') === 'connect_api-auth_check';
    }

    public function getCredentials(Request $request)
    {
        return $this->fetchAccessToken($this->getApiAuthClient());
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        /**
         * @var ResourceOwner $apiAuthUser
         */
        $apiAuthUser = $this->getApiAuthClient()
            ->fetchUserFromToken($credentials);

        $email = $apiAuthUser->getEmail();

        // 1) Check if we already know the user
        $existingUser = $this->em->getRepository(User::class)
            ->findOneBy(['email' => $apiAuthUser->getEmail()]);
        if ($existingUser) {
            return $existingUser;
        }

        // 2) If not, let's create them
        $user = new User();
        $user->setEmail($apiAuthUser->getEmail());
        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $targetUrl = $this->router->generate('home');

        return new RedirectResponse($targetUrl);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        return new Response($message, Response::HTTP_FORBIDDEN);
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse(
            '/connect/api-auth',
            Response::HTTP_TEMPORARY_REDIRECT
        );
    }

    private function getApiAuthClient()
    {
        return $this->clientRegistry->getClient('api-auth');
    }
}
