<?php

namespace App\Tests\Functional\Controller;

use Doctrine\Bundle\DoctrineBundle\DataCollector\DoctrineDataCollector;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiCacheTest extends WebTestCase
{
    use RefreshDatabaseTrait;

    /**
     * @var KernelBrowser
     */
    private $client;

    public function setUp()
    {
        $this->client = static::createClient();
    }

    public function testQueryCache()
    {
        // First request
        $response = $this->makeRequest();

        $this->assertEquals(200, $response->getStatusCode());

        $profile = $this->client->getProfile();
        /**
         * @var DoctrineDataCollector $dbCollector
         */
        $dbCollector = $profile->getCollector("db");
        $queryCount = $dbCollector->getQueryCount();

        // Database is queried
        $this->assertGreaterThan(0, $queryCount);

        // Second request
        $this->client->restart();
        $response = $this->makeRequest();

        $this->assertEquals(200, $response->getStatusCode());

        $profile = $this->client->getProfile();
        /**
         * @var DoctrineDataCollector $dbCollector
         */
        $dbCollector = $profile->getCollector("db");
        $queryCount = $dbCollector->getQueryCount();

        // Database is not queried
        $this->assertEquals(0, $queryCount);
    }

    private function makeRequest()
    {
        $apiKey = self::$container->getParameter("active_api_key");
        $this->client->enableProfiler();
        $this->client->request(
            "GET",
            "/api/geo/communes/74305",
            [],
            [],
            [
                "HTTP_X-Api-Key" => $apiKey
            ]
        );

        return $this->client->getResponse();
    }
}
