<?php

namespace App\Entity\Analytics;

use App\Entity\Api;
use App\Entity\Application;
use App\Type\PrintableDateTimeImmutable;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Request
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="iddatetime")
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $time;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Application")
     * @ORM\JoinColumn(nullable=false)
     */
    private $application;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Api")
     * @ORM\JoinColumn(nullable=false)
     */
    private $api;

    /**
     * @ORM\Column(type="smallint")
     */
    private $statusCode;

    /**
     * @ORM\Column(type="integer")
     */
    private $responseTime;

    public function __construct()
    {
        $this->time = new PrintableDateTimeImmutable();
    }

    public function getTime(): DateTimeImmutable
    {
        return $this->time;
    }

    public function getApplication(): ?Application
    {
        return $this->application;
    }

    public function setApplication(?Application $application): self
    {
        $this->application = $application;

        return $this;
    }

    public function getApi(): ?Api
    {
        return $this->api;
    }

    public function setApi(?Api $api): self
    {
        $this->api = $api;

        return $this;
    }

    public function getStatusCode(): ?int
    {
        return $this->statusCode;
    }

    public function setStatusCode(int $statusCode): self
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    public function getResponseTime(): ?int
    {
        return $this->responseTime;
    }

    public function setResponseTime(int $responseTime): self
    {
        $this->responseTime = $responseTime;

        return $this;
    }
}
