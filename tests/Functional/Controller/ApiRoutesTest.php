<?php

namespace App\Tests\Functional\Controller;

use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiRoutesTest extends WebTestCase
{
    use RefreshDatabaseTrait;

    public function testGeoRoute()
    {
        $client = static::createClient();
        $apiKey = self::$container->getParameter("active_api_key");

        $client->request(
            "GET",
            "/api/geo/communes/74305",
            [],
            [],
            [
                "HTTP_X-Api-Key" => $apiKey
            ]
        );

        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertEquals("Ville-la-Grand", $content["nom"]);
    }

    public function testQueryParams()
    {
        $client = static::createClient();
        $apiKey = self::$container->getParameter("active_api_key");

        $client->request(
            "GET",
            "/api/geo/communes",
            [
                "codePostal" => 74100
            ],
            [],
            [
                "HTTP_X-Api-Key" => $apiKey
            ]
        );

        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertEquals(6, count($content));
    }
}
