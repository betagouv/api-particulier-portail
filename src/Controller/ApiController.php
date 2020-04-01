<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

class ApiController
{
    public function backend(string $uri)
    {
        return new Response($uri);
    }
}
