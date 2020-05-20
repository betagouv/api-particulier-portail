<?php

namespace App\Tests\Unit\Controller;

use App\Controller\ApiController;
use App\Entity\Api;
use App\Entity\Application;
use App\Repository\ApiRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag;
use Symfony\Component\Security\Core\Security;

class ApiControllerTest extends TestCase
{
    /**
     * @var ApiController
     */
    private $controller;

    /**
     * @var ApiRepository|MockObject
     */
    private $apiRepository;

    /**
     * @var Security|MockObject
     */
    private $security;

    /**
     * @var Api
     */
    private $api;

    /**
     * @var Application
     */
    private $application;

    public function setUp()
    {
        $this->apiRepository = $this->createMock(ApiRepository::class);
        $this->application = $this->createMock(Application::class);
        $this->security = $this->createMock(Security::class);
        $this->security->method("getUser")
            ->willReturn($this->application);
        $this->api = $this->createMock(Api::class);
        $this->apiRepository->method("find")
            ->willReturn($this->api);
    }

    private function init(array $responses = null)
    {
        $responses = $responses ?? [new MockResponse()];
        $httpClient = new MockHttpClient($responses);

        $this->controller = new ApiController(
            $httpClient,
            [],
        );
    }

    public function testTimeout()
    {
        $this->init([
            new MockResponse((function () {
                // Simulate backend timeout
                yield '';
            })()),
        ]);
        $apiResponse = $this->makeRequest();
        $this->assertEquals(Response::HTTP_GATEWAY_TIMEOUT, $apiResponse->getStatusCode());
    }

    public function statusCodes()
    {
        return array_map(function (int $code) {
            return [$code];
        }, array_keys(Response::$statusTexts));
    }

    /**
     * @dataProvider statusCodes
     */
    public function testStatusCode(int $statusCode)
    {
        $this->init([
            new MockResponse("", [
                "http_code" => $statusCode
            ]),
        ]);

        /**
         * @var Response $apiResponse
         */
        $apiResponse = $this->makeRequest();
        $this->assertEquals($statusCode, $apiResponse->getStatusCode());
    }

    private function makeRequest()
    {
        /**
         * @var Request|MockObject $request
         */
        $request = $this->createMock(Request::class);
        $request->method("getMethod")
            ->willReturn("GET");
        $request->attributes = new AttributeBag();
        return $this->controller->backend(
            "yolo",
            "http://croute",
            $request,
            "api"
        );
    }
}
