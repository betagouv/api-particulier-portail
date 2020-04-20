<?php

namespace App\Tests\Unit\Controller;

use App\Collector\AnalyticsCollectorInterface;
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
use Symfony\Component\Security\Core\Security;

class ApiControllerTest extends TestCase
{
    /**
     * @var ApiController
     */
    private $controller;

    /**
     * @var AnalyticsCollectorInterface|MockObject
     */
    private $analyticsCollector;

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
        $this->analyticsCollector = $this->createMock(AnalyticsCollectorInterface::class);
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
            $this->analyticsCollector,
            $this->apiRepository,
            $this->security
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
        $this->analyticsCollector
            ->expects($this->once())
            ->method("collectCall")
            ->with(
                $this->api,
                $this->application,
                "yolo",
                Response::HTTP_GATEWAY_TIMEOUT
            );
        $this->makeRequest();
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
        $this->analyticsCollector
            ->expects($this->once())
            ->method("collectCall")
            ->with(
                $this->api,
                $this->application,
                "yolo",
                $statusCode
            );

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
        return $this->controller->backend(
            "yolo",
            "http://croute",
            $request,
            "api"
        );
    }
}
