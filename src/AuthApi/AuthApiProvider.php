<?php

namespace App\AuthApi;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;

class AuthApiProvider extends AbstractProvider
{
    use BearerAuthorizationTrait;

    /**
     * @var string
     */
    private $baseUrl;

    public function __construct(array $options = [], array $collaborators = [])
    {
        parent::__construct($options, $collaborators);
        $this->baseUrl = $options["base_url"];
    }

    const ACCESS_TOKEN_RESOURCE_OWNER_ID = "sub";

    public function getBaseAuthorizationUrl()
    {
        return $this->baseUrl . "/oauth/authorize";
    }

    public function getBaseAccessTokenUrl(array $params)
    {
        return $this->baseUrl . "/oauth/token";
    }

    public function getResourceOwnerDetailsUrl(AccessToken $accessToken)
    {
        return $this->baseUrl . "/oauth/userinfo";
    }

    protected function getDefaultScopes()
    {
        return ["openid email roles organizations profile"];
    }

    protected function checkResponse(ResponseInterface $response, $data)
    {
    }

    protected function createResourceOwner(array $response, AccessToken $token)
    {
        $organizations = array_map(function (array $organization) {
            return $organization["siret"];
        }, $response["organizations"]);
        return new ResourceOwner(
            $response["sub"],
            $response["given_name"],
            $response["family_name"],
            $response["email"],
            $response["roles"],
            $organizations
        );
    }
}
