<?php

namespace App\Tests\Functional\Controller;

use App\Repository\UserRepository;
use DateTimeImmutable;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use League\OAuth2\Server\CryptKey;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Trikoder\Bundle\OAuth2Bundle\League\Repository\AccessTokenRepository;
use Trikoder\Bundle\OAuth2Bundle\League\Repository\ClientRepository;

class UserInfoControllerTest extends WebTestCase
{
    use RefreshDatabaseTrait;

    public function testAccessWithToken()
    {
        $client = $this->createClient();
        $accessToken = $this->createAccessToken();

        $client->request(
            "GET",
            "/oauth/userinfo",
            [],
            [],
            [
                "HTTP_Authorization" => sprintf("Bearer %s", (string) $accessToken)
            ]
        );
        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $userInfo = json_decode($response->getContent(), true);
        $this->assertEquals("jean@moust.com", $userInfo["email"]);
    }

    private function createAccessToken()
    {
        /**
         * @var ClientRepository $clientRepository
         */
        $clientRepository = self::$container->get(ClientRepository::class);
        /**
         * @var AccessTokenRepository $accessTokenRepository
         */
        $accessTokenRepository = self::$container->get(AccessTokenRepository::class);
        /**
         * @var UserRepository $userRepository
         */
        $userRepository = self::$container->get(UserRepository::class);

        $client = $clientRepository->getClientEntity("the-test-client");
        $user = $userRepository->findOneBy(["email" => "jean@moust.com"]);

        $accessToken = $accessTokenRepository->getNewToken($client, [], $user->getEmail());
        $accessToken->setIdentifier("la barbe de la femme à Georges Moustaki");
        $accessToken->setExpiryDateTime((new DateTimeImmutable())->modify("+1 hour"));
        $accessToken->setPrivateKey(new CryptKey(__DIR__ . "/../../../var/oauth/private.key"));
        $accessTokenRepository->persistNewAccessToken($accessToken);

        return $accessToken;
    }
}
