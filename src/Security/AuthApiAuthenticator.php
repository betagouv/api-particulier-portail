<?php

namespace App\Security;

use App\Entity\User;
use App\AuthApi\ResourceOwner;
use App\Event\UserCreatedEvent;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class AuthApiAuthenticator extends SocialAuthenticator
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

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var string
     */
    private $dashboardUrl;

    public function __construct(
        ClientRegistry $clientRegistry,
        EntityManagerInterface $em,
        RouterInterface $router,
        EventDispatcherInterface $eventDispatcher,
        SessionInterface $session,
        string $dashboardUrl
    ) {
        $this->clientRegistry = $clientRegistry;
        $this->em = $em;
        $this->router = $router;
        $this->eventDispatcher = $eventDispatcher;
        $this->session = $session;
        $this->dashboardUrl = $dashboardUrl;
    }

    public function supports(Request $request)
    {
        // continue ONLY if the current ROUTE matches the check ROUTE
        return $request->attributes->get('_route') === 'connect_auth-api_check';
    }

    public function getCredentials(Request $request)
    {
        return $this->fetchAccessToken($this->getAuthApiClient());
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        /**
         * @var ResourceOwner $authApiUser
         */
        $authApiUser = $this->getAuthApiClient()
            ->fetchUserFromToken($credentials);

        // 1) Check if we already know the user
        $existingUser = $this->em->getRepository(User::class)
            ->findOneBy(['email' => $authApiUser->getEmail()]);
        if ($existingUser) {
            return $existingUser;
        }

        // 2) If not, let's create them
        $user = new User();
        $user->setEmail($authApiUser->getEmail());
        $user->setName($authApiUser->getName());
        $user->setSurname($authApiUser->getSurname());
        $this->em->persist($user);
        $this->em->flush();

        $this->eventDispatcher->dispatch(new UserCreatedEvent($user));

        return $user;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $targetUrl = $this->session->get('original_target') ?? $this->router->generate('easyadmin');
        $this->session->remove('original_target');

        // If targetUrl if for the dashboard login, then redirect to the actual portal login route
        if ($targetUrl === $this->dashboardUrl . '/login') {
            $targetUrl = $targetUrl . '/generic_oauth';
        }

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
            '/connect/auth-api',
            Response::HTTP_TEMPORARY_REDIRECT
        );
    }

    private function getAuthApiClient()
    {
        return $this->clientRegistry->getClient('auth-api');
    }
}
