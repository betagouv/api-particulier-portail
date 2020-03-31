<?php

namespace App\Signup;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Response;

class SignupProvider extends AbstractProvider
{
    use BearerAuthorizationTrait;
    const ACCESS_TOKEN_RESOURCE_OWNER_ID = "sub";

    public function getBaseAuthorizationUrl()
    {
        return "https://auth-staging.api.gouv.fr/oauth/authorize";
    }

    public function getBaseAccessTokenUrl(array $params)
    {
        return "https://auth-staging.api.gouv.fr/oauth/token";
    }

    public function getResourceOwnerDetailsUrl(AccessToken $accessToken)
    {
        return "https://auth-staging.api.gouv.fr/oauth/userinfo";
    }

    protected function getDefaultScopes()
    {
        return ["openid email roles organizations profile"];
    }

    protected function checkResponse(ResponseInterface $response, $data)
    {
        $responseIsValid = $response->getStatusCode() === Response::HTTP_OK && isset($data["access_token"]) && isset($data["id_token"]) && isset($data["scope"]);
        if ($responseIsValid)
            return;
        throw new IdentityProviderException("Failed to authenticate", Response::HTTP_UNAUTHORIZED, []);
    }

    protected function createResourceOwner(array $response, AccessToken $token)
    {
        $organizations = array_map(function (array $organization) {
            return $organization["siret"];
        }, $response["organizations"]);
        return new ResourceOwner($response["sub"], $response["family_name"], $response["given_name"], $response["email"], $response["roles"], $organizations);
    }
}
