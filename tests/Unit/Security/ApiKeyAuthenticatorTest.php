<?php

namespace App\Tests\Unit\Security;

use App\Repository\ApplicationRepository;
use App\Security\ApiKeyAuthenticator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class ApiKeyAuthenticatorTest extends TestCase
{
    /**
     * @var ApplicationRepository|MockObject
     */
    private $applicationRepository;

    /**
     * @var ApiKeyAuthenticator
     */
    private $apiKeyAuthenticator;

    public function setUp()
    {
        /**
         * @var ApplicationRepository $applicationRepository
         */
        $this->applicationRepository = $this->createMock(ApplicationRepository::class);
        $this->apiKeyAuthenticator = new ApiKeyAuthenticator($this->applicationRepository);
    }

    public function testCorrectCredentials()
    {
        $request = Request::create(
            "/api/geo/communes/74305",
            "GET",
            [],
            [],
            [],
            [
                "HTTP_X-Api-Key" => "yolo"
            ]
        );

        $credentials = $this->apiKeyAuthenticator->getCredentials($request);
        $expectedCredentials = [
            "path" => "geo",
            "apiKey" => "yolo"
        ];

        $this->assertEquals($expectedCredentials, $credentials);
    }

    public function testMissingApiKey()
    {
        $request = Request::create(
            "/api/geo/communes/74305",
            "GET",
            [],
            [],
            [],
            [
                "HTTP_X-Api-Croute" => "yolo"
            ]
        );

        $credentials = $this->apiKeyAuthenticator->getCredentials($request);
        $expectedCredentials = null;

        $this->assertEquals($expectedCredentials, $credentials);
    }
}
