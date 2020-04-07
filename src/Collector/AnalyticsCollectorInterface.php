<?php

namespace App\Collector;

use App\Entity\Api;
use App\Entity\Application;

interface AnalyticsCollectorInterface
{
    public function collectCall(
        Api $api,
        Application $application,
        string $uri,
        int $backendResponseStatusCode,
        int $backendResponseTime
    ): void;
}
