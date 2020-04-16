<?php

namespace App\Collector;

use App\Entity\Api;
use App\Entity\Application;
use DateTime;
use InfluxDB\Client;
use InfluxDB\Database;
use InfluxDB\Point;

class InfluxDBAnalyticsCollector implements AnalyticsCollectorInterface
{
    /**
     * @var Database
     */
    private $database;

    public function __construct(Client $client, string $database)
    {
        $this->database = $client->selectDB($database);
    }

    public function collectCall(
        Api $api,
        Application $application,
        string $uri,
        int $backendResponseStatusCode,
        int $backendResponseTime
    ): void {
        $tags = [
            "api_id" => $api->getId(),
            "api_name" => $api->getName(),
            "application_id" => $application->getId(),
            "application_name" => $application->getName(),
            "organization_id" => $application->getOrganization()->getId(),
            "organization_siret" => $application->getOrganization()->getSiret()
        ];

        $fields = [
            "uri" => $uri,
            "backend_response_time" => $backendResponseTime,
            "backend_response_status_code" => $backendResponseStatusCode
        ];

        $point = new Point(
            "calls",
            null,
            $tags,
            $fields,
            (new DateTime())->getTimestamp()
        );

        $this->database->writePoints([$point], Database::PRECISION_SECONDS);
    }
}
