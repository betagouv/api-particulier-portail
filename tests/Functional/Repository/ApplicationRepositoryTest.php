<?php

namespace App\Tests\Functional\Repository;

use App\Repository\ApplicationRepository;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApplicationRepositoryTest extends WebTestCase
{
    use RefreshDatabaseTrait;

    public function testFindOneByApiKeyHashAndApiPath()
    {
        static::bootKernel();
        $activeApiKeyHash = self::$container->getParameter("active_api_key_hash");
        $inactiveApiKeyHash = self::$container->getParameter("inactive_api_key_hash");

        // Cannot use a dataProvider here since we need the kernel to be booted before being able to access the parameters
        $testCases = [
            [$activeApiKeyHash, "geo", true],             // OK
            [$activeApiKeyHash, "entreprise", true],      // OK
            [$activeApiKeyHash, "lol", false],            // api not existing, KO
            ["croute", "geo", false],          // api key not existing, KO
            [$inactiveApiKeyHash, "geo", false],         // api key inactive, KO
            [$inactiveApiKeyHash, "poke", false]            // subscription inactive, KO
        ];

        /**
         * @var ApplicationRepository $repository
         */
        $repository = static::$container->get(ApplicationRepository::class);

        foreach ($testCases as $testCase) {
            $apiKey = $testCase[0];
            $apiPath = $testCase[1];
            $shouldExist = $testCase[2];

            $application = $repository->findOneByApiKeyHashAndApiPath($apiKey, $apiPath);

            if ($shouldExist) {
                $this->assertNotNull($application, "Application must exist with provided api key hash and api path.");
            } else {
                $this->assertNull($application, "Application musn't exist with provided api key hash and api path.");
            }
        }
    }
}
