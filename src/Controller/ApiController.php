<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

    public function __construct(HttpClientInterface $httpClient, array $proxiedHttpHeaders)
    {
        $this->httpClient = $httpClient;
        $this->proxiedHttpHeaders = $proxiedHttpHeaders;
    }

    public function backend(string $uri, string $backend, Request $request)
    {
        $response = $this->httpClient->request(
            $request->getMethod(),
            sprintf("%s/%s", $backend, $uri),
        );

        $content = $response->getContent();
        $headers = [];
        foreach ($this->proxiedHttpHeaders as $proxiedHttpHeader) {
            $lowerCaseProxiedHeader = strtolower($proxiedHttpHeader);
            if (isset($response->getHeaders()[$lowerCaseProxiedHeader])) {
                $headers[$lowerCaseProxiedHeader] = $response->getHeaders()[$lowerCaseProxiedHeader];
            }
        }

        return new Response($content, $response->getStatusCode(), $headers);
    }
}
