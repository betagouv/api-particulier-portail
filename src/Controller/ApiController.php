<?php

namespace App\Controller;

use App\Collector\AnalyticsCollectorInterface;
use App\Entity\Application;
use App\Repository\ApiRepository;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpClient\Exception\RedirectionException;
use Symfony\Component\HttpClient\Exception\ServerException;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiController
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

    /**
     * @var Security
     */
    private $security;

    public function __construct(
        HttpClientInterface $httpClient,
        array $proxiedHttpHeaders,
        AnalyticsCollectorInterface $analyticsCollector,
        ApiRepository $apiRepository,
        Security $security
    ) {
        $this->httpClient = $httpClient;
        $this->proxiedHttpHeaders = $proxiedHttpHeaders;
        $this->analyticsCollector = $analyticsCollector;
        $this->apiRepository = $apiRepository;
        $this->security = $security;
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

        $content = null;
        $statusCode = null;
        try {
            $content = $response->getContent();
            $statusCode = $response->getStatusCode();
        } catch (TransportException $e) {
            $statusCode = Response::HTTP_GATEWAY_TIMEOUT;
        } catch (ClientException | ServerException | RedirectionException $e) {
            $statusCode = $response->getStatusCode();
        }

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
        $application = $this->security->getUser();

        $api = $this->apiRepository->find($apiId);

        $this->analyticsCollector->collectCall(
            $api,
            $application,
            $uri,
            $statusCode,
            $requestEvent->getDuration()
        );

        return new Response($content, $statusCode, $headers);
    }
}
