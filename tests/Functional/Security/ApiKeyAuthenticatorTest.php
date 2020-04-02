<?php

namespace App\Tests\Functional\Security;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyAuthenticatorTest extends WebTestCase
{
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
        $this->client->request(
            "GET",
            "/api/geo/communes/74305",
            [],
            [],
            [
                "HTTP_X-Api-Key" => "yolo"
            ]
        );

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    public function testInvalidApiKey()
    {
        $this->client->request(
            "GET",
            "/api/geo/communes/74305",
            [],
            [],
            [
                "HTTP_X-Api-Key" => "croute"
            ]
        );

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
    }
}
