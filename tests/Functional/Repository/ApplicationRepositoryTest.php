<?php

namespace App\Tests\Functional\Repository;

use App\Repository\ApplicationRepository;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApplicationRepositoryTest extends WebTestCase
{
    use RefreshDatabaseTrait;

    /**
     * @dataProvider apiKeyHashAndApiPathProvider
     */
    public function testFindOneByApiKeyHashAndApiPath(string $apiKeyHash, string $apiPath, bool $shouldExist)
    {
        static::bootKernel();

        /**
         * @var ApplicationRepository $repository
         */
        $repository = static::$container->get(ApplicationRepository::class);

        $application = $repository->findOneByApiKeyHashAndApiPath($apiKeyHash, $apiPath);

        if ($shouldExist) {
            $this->assertNotNull($application, "Application must exist with provided api key hash and api path.");
        } else {
            $this->assertNull($application, "Application musn't exist with provided api key hash and api path.");
        }
    }

    public function apiKeyHashAndApiPathProvider()
    {
        return [
            ["yolo", "geo", true],             // OK
            ["yolo", "entreprise", true],      // OK
            ["yolo", "lol", false],            // api not existing, KO
            ["croute", "geo", false],          // api key not existing, KO
            ["georges", "geo", false],         // api key inactive, KO
            ["yolo", "poke", false]            // subscription inactive, KO
        ];
    }
}
