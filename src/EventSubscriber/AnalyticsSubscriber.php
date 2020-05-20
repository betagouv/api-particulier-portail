<?php

namespace App\EventSubscriber;

use App\Entity\Application;
use App\Repository\ApiRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Security;
use App\Entity\Analytics\Request;
use DateTimeImmutable;
use Doctrine\ORM\EntityManager;
use Exception;

class AnalyticsSubscriber implements EventSubscriberInterface
{
    /**
     * @var ApiRepository
     */
    private $apiRepository;

    /**
     * @var Security
     */
    private $security;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var bool
     */
    private $enabled;

    public function __construct(ApiRepository $apiRepository, Security $security, EntityManagerInterface $entityManager, bool $enabled)
    {
        $this->apiRepository = $apiRepository;
        $this->security = $security;
        $this->entityManager = $entityManager;
        $this->enabled = $enabled;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::TERMINATE => 'onKernelTerminate'
        ];
    }

    public function onKernelTerminate(KernelEvent $event)
    {
        if (!$this->enabled) {
            return;
        }
        $request = $event->getRequest();

        if (!$request->get("collect_analytics")) {
            return;
        }
        if (is_null($this->security->getUser())) {
            return;
        }

        /**
         * @var sttring $apiId
         */
        $apiId = $request->get("apiId");

        /**
         * @var Application $application
         */
        $application = $this->security->getUser();

        $api = $this->apiRepository->find($apiId);

        $statusCode = $request->get("status_code");
        $responseTime = $request->get("response_time");

        $analyticsRequest = new Request();
        $analyticsRequest->setApi($api);
        $analyticsRequest->setApplication($application);
        $analyticsRequest->setStatusCode($statusCode);
        $analyticsRequest->setResponseTime($responseTime);

        $this->entityManager->persist($analyticsRequest);
        $this->entityManager->flush();
    }
}
