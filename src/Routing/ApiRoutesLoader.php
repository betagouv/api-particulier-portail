<?php

namespace App\Routing;

use App\Repository\ApiRepository;
use RuntimeException;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class ApiRoutesLoader extends Loader
{
    private $isLoaded = false;
    private $apiRepository;

    public function __construct(ApiRepository $apiRepository)
    {
        $this->apiRepository = $apiRepository;
    }

    public function load($resource, string $type = null)
    {
        if (true === $this->isLoaded) {
            throw new RuntimeException('Do not add the "extra" loader twice');
        }

        $routes = new RouteCollection();

        $apis = $this->apiRepository->findAll();

        foreach ($apis as $api) {
            $path = sprintf("/api/%s/{uri}", $api->getPath());
            $defaults = [
                "_controller" => "App\Controller\ApiController::backend",
                "backend" => $api->getBackend(),
                "apiId" => $api->getId()->toString()
            ];
            $requirements = [
                "uri" => ".+"
            ];

            $route = new Route($path, $defaults, $requirements);

            $routeName = $api->getName();
            $routes->add($routeName, $route);
        }

        $this->isLoaded = true;

        return $routes;
    }

    public function supports($resource, string $type = null)
    {
        return 'extra' === $type;
    }
}
