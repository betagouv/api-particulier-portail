<?php

namespace App\Controller;

use App\Collector\AnalyticsCollectorInterface;
use App\Entity\Api;
use App\Entity\Application;
use App\Repository\ApiRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiController extends AbstractController
{
    /**
     * @var HttpClientInterface
     */
    private $httpClient;

    /**
     * @var string[]
     */
    private $proxiedHttpHeaders;

    /**
     * @var AnalyticsCollectorInterface
     */
    private $analyticsCollector;

    /**
     * @var ApiRepository
     */
    private $apiRepository;

    public function __construct(
        HttpClientInterface $httpClient,
        array $proxiedHttpHeaders,
        AnalyticsCollectorInterface $analyticsCollector,
        ApiRepository $apiRepository
    ) {
        $this->httpClient = $httpClient;
        $this->proxiedHttpHeaders = $proxiedHttpHeaders;
        $this->analyticsCollector = $analyticsCollector;
        $this->apiRepository = $apiRepository;
    }

    public function backend(
        string $uri,
        string $backend,
        Request $request,
        string $apiId
    ) {
        $stopWatch = new Stopwatch();
        $stopWatch->start("backend_request");

        $response = $this->httpClient->request(
            $request->getMethod(),
            sprintf("%s/%s", $backend, $uri),
        );
        $content = $response->getContent();

        $requestEvent = $stopWatch->stop("backend_request");

        $headers = [];
        foreach ($this->proxiedHttpHeaders as $proxiedHttpHeader) {
            $lowerCaseProxiedHeader = strtolower($proxiedHttpHeader);
            if (isset($response->getHeaders()[$lowerCaseProxiedHeader])) {
                $headers[$lowerCaseProxiedHeader] = $response->getHeaders()[$lowerCaseProxiedHeader];
            }
        }

        /**
         * @var Application $application
         */
        $application = $this->getUser();

        $api = $this->apiRepository->find($apiId);

        $this->analyticsCollector->collectCall(
            $api,
            $application,
            $uri,
            $response->getStatusCode(),
            $requestEvent->getDuration()
        );

        return new Response($content, $response->getStatusCode(), $headers);
    }
}
