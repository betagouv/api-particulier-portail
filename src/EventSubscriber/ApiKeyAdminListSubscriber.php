<?php

namespace App\EventSubscriber;

use App\Entity\ApiKey;
use App\Entity\Application;
use App\Entity\User;
use App\Entity\UserPosition;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Event\EasyAdminEvents;
use phpDocumentor\Reflection\DocBlock\Tags\Generic;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Security\Core\Security;

class ApiKeyAdminListSubscriber implements EventSubscriberInterface
{
    /**
     * @var Security
     */
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public static function getSubscribedEvents()
    {
        return [
            EasyAdminEvents::POST_LIST_QUERY_BUILDER => "postListQueryBuilder"
        ];
    }

    public function postListQueryBuilder(GenericEvent $event)
    {
        /**
         * @var QueryBuilder $queryBuilder
         */
        $queryBuilder = $event["query_builder"];

        if (!in_array(ApiKey::class, $queryBuilder->getRootEntities())) {
            return;
        }

        /**
         * @var User $user
         */
        $user = $this->security->getUser();

        $event["query_builder"] = $queryBuilder
            ->innerJoin(Application::class, "app", "WITH", sprintf("app.id = %s.application", $queryBuilder->getRootAlias()))
            ->innerJoin(UserPosition::class, "userPos", "WITH", "userPos.application = app.id")
            ->andWhere("userPos.user = :user")
            ->setParameter("user", $user->getId()->toString());
    }
}
