<?php

namespace App\Tests\Unit\Security;

use App\Repository\ApplicationRepository;
use App\Security\ApiKeyAuthenticator;
use App\Security\ApiKeyEncoder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;

class ApiKeyAuthenticatorTest extends TestCase
{
    /**
     * @var ApplicationRepository|MockObject
     */
    private $applicationRepository;

    /**
     * @var ApiKeyEncoder|MockObject
     */
    private $apiKeyEncoder;

    /**
     * @var ApiKeyAuthenticator
     */
    private $apiKeyAuthenticator;

    /**
     * @var LoggerInterface|MockObject
     */
    private $logger;

    public function setUp()
    {
        /**
         * @var ApplicationRepository $applicationRepository
         */
        $this->applicationRepository = $this->createMock(ApplicationRepository::class);

        /**
         * @var LoggerInterface $logger
         */
        $this->logger = $this->createMock(LoggerInterface::class);
        /**
         * @var ApiKeyEncoder $apiKeyEncoder
         */
        $this->apiKeyEncoder = $this->createMock(ApiKeyEncoder::class);
        $this->apiKeyAuthenticator = new ApiKeyAuthenticator(
            $this->applicationRepository,
            $this->apiKeyEncoder,
            $this->logger
        );
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
