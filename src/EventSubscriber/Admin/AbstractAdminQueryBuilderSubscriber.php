<?php

namespace App\EventSubscriber\Admin;

use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Event\EasyAdminEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Security\Core\Security;

abstract class AbstractAdminQueryBuilderSubscriber implements EventSubscriberInterface
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
            EasyAdminEvents::POST_LIST_QUERY_BUILDER => "postQueryBuilder",
            EasyAdminEvents::POST_SEARCH_QUERY_BUILDER => "postQueryBuilder"
        ];
    }

    public function postQueryBuilder(GenericEvent $event)
    {
        $queryBuilder = $event["query_builder"];

        if (!in_array($this->getTargetEntity(), $queryBuilder->getRootEntities())) {
            return;
        }

        $event["query_builder"] = $this->getNewQueryBuilder($queryBuilder);
    }

    protected function getUser(): User
    {
        return $this->security->getUser();
    }

    protected abstract function getNewQueryBuilder(QueryBuilder $queryBuilder): QueryBuilder;

    protected abstract function getTargetEntity(): string;
}
