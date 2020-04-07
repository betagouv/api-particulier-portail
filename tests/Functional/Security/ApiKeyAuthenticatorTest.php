<?php

namespace App\Tests\Functional\Security;

use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyAuthenticatorTest extends WebTestCase
{
    use RefreshDatabaseTrait;

    /**
     * @var KernelBrowser
     */
    private $client;

    public function setUp()
    {
        $this->client = $this->createClient();
    }

    public function testValidApiKeyAndApiPath()
    {
        $apiKey = self::$container->getParameter("active_api_key");

        $this->client->request(
            "GET",
            "/api/geo/communes/74305",
            [],
            [],
            [
                "HTTP_X-Api-Key" => $apiKey
            ]
        );

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    public function testInvalidApiKey()
    {
        $apiKey = self::$container->getParameter("inactive_api_key");

        $this->client->request(
            "GET",
            "/api/geo/communes/74305",
            [],
            [],
            [
                "HTTP_X-Api-Key" => $apiKey
            ]
        );

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
    }
}
