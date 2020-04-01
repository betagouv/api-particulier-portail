<?php

namespace App\Tests\Controller;

use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiRoutesTest extends WebTestCase
{
    use RefreshDatabaseTrait;

    public function testGeoRoute()
    {
        $client = static::createClient();

        $client->request("GET", "/api/geo/communes/74305");

        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertEquals("Ville-la-Grand", $content["nom"]);
    }
}
