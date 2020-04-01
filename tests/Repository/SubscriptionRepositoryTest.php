<?php

namespace App\Tests\Repository;

use App\Repository\SubscriptionRepository;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SubscriptionRepositoryTest extends WebTestCase
{
    use RefreshDatabaseTrait;

    /**
     * @dataProvider apiKeyAndPathProvider 
     */
    public function testSubscriptionExists($apiKeyHash, $path, $shouldExist)
    {
        static::bootKernel();

        /**
         * @var SubscriptionRepository $repository
         */
        $repository = static::$container->get(SubscriptionRepository::class);

        $exists = $repository->activeSubscriptionExistsForApiKeyHashAndPath($apiKeyHash, $path);
        $this->assertEquals($shouldExist, $exists);
    }

    public function apiKeyAndPathProvider()
    {
        return [
            ["yolo", "/geo", true],             // OK
            ["yolo", "/entreprise", true],      // OK
            ["yolo", "/lol", false],            // api not existing, KO
            ["croute", "/geo", false],          // api key not existing, KO
            ["georges", "/geo", false],         // api key inactive, KO
            ["yolo", "/poke", false]            // subscription inactive, KO
        ];
    }
}
