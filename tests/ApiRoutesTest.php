<?php

namespace App\Tests;

use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiRoutesTest extends WebTestCase
{
    use RefreshDatabaseTrait;

    public function testGeoRoute()
    {
        $client = static::createClient();

        $client->request("GET", "/api/geo/communes/74305");

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
