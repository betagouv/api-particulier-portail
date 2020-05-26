<?php

namespace App\Security;

use App\Repository\ApplicationRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class ApiKeyAuthenticator extends AbstractGuardAuthenticator
{
    /**
     * @var ApplicationRepository
     */
    private $applicationRepository;

    /**
     * @var ApiKeyEncoder
     */
    private $apiKeyEncoder;

    /**
     * @var LoggerInterface
     */
    private $logger;

    const HEADER_NAME = "X-Api-Key";

    public function __construct(ApplicationRepository $applicationRepository, ApiKeyEncoder $apiKeyEncoder, LoggerInterface $logger)
    {
        $this->applicationRepository = $applicationRepository;
        $this->apiKeyEncoder = $apiKeyEncoder;
        $this->logger = $logger;
    }

    public function supports(Request $request)
    {
        return $request->headers->has(self::HEADER_NAME);
    }

    public function getCredentials(Request $request)
    {
        $apiKey = $request->headers->get(self::HEADER_NAME);


        if (is_null($apiKey)) {
            return null;
        }

        $components = explode("/", $request->getPathInfo());

        // Expect at least 4 components, the request looks like "/api/{the target API path}/{the target API URI}"
        if (count($components) < 4) {
            return null;
        }

        return [
            "path" => $components[2],
            "apiKey" => $apiKey
        ];
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        if (null === $credentials) {
            // The token header was empty, authentication fails with HTTP Status
            // Code 401 "Unauthorized"
            return null;
        }

        $apiKeyHash = $this->apiKeyEncoder->encodeString($credentials["apiKey"]);

        // if an Application is returned, checkCredentials() is called
        return $this->applicationRepository->findOneByApiKeyHashAndApiPath(
            $apiKeyHash,
            $credentials["path"]
        );
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        // Check credentials - e.g. make sure the password is valid.
        // In case of an API token, no credential check is needed.

        // Return `true` to cause authentication success
        return true;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        // on success, let the request continue
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $this->logger->debug('Authentication failed', [
            'message' => $exception->getMessageKey(),
            'message_data' => $exception->getMessageData()
        ]);
        $data = [
            // you may want to customize or obfuscate the message first
            'message' => 'Authentication failed'

            // or to translate this message
            // $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Called when authentication is needed, but it's not sent
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $data = [
            // you might translate this message
            'message' => 'Authentication Required'
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    public function supportsRememberMe()
    {
        return false;
    }
}
