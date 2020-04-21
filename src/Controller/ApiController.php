<?php

namespace App\Controller;

use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpClient\Exception\RedirectionException;
use Symfony\Component\HttpClient\Exception\ServerException;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

    public function __construct(
        HttpClientInterface $httpClient,
        array $proxiedHttpHeaders
    ) {
        $this->httpClient = $httpClient;
        $this->proxiedHttpHeaders = $proxiedHttpHeaders;
    }

    public function backend(
        string $uri,
        string $backend,
        Request $request
    ) {
        $stopWatch = new Stopwatch();
        $stopWatch->start("backend_request");

        $response = $this->httpClient->request(
            $request->getMethod(),
            sprintf("%s/%s", $backend, $uri),
        );

        $content = null;
        $statusCode = null;
        $headers = [];
        try {
            $content = $response->getContent();
            $statusCode = $response->getStatusCode();
            foreach ($this->proxiedHttpHeaders as $proxiedHttpHeader) {
                $lowerCaseProxiedHeader = strtolower($proxiedHttpHeader);
                if (isset($response->getHeaders()[$lowerCaseProxiedHeader])) {
                    $headers[$lowerCaseProxiedHeader] = $response->getHeaders()[$lowerCaseProxiedHeader];
                }
            }
        } catch (TransportException $e) {
            $statusCode = Response::HTTP_GATEWAY_TIMEOUT;
        } catch (ClientException | ServerException | RedirectionException $e) {
            $statusCode = $response->getStatusCode();
        }

        $requestEvent = $stopWatch->stop("backend_request");

        $request->attributes->set("status_code", $statusCode);
        $request->attributes->set("response_time", $requestEvent->getDuration());

        return new Response($content, $statusCode, $headers);
    }
}
