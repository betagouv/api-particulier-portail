<?php

namespace App\EventSubscriber\Admin;

use App\Entity\Application;
use App\Entity\UserPosition;
use Doctrine\ORM\QueryBuilder;

class ApplicationAdminQueryBuilderSubscriber extends AbstractAdminQueryBuilderSubscriber
{
    protected function getTargetEntity(): string
    {
        return Application::class;
    }

    protected function getNewQueryBuilder(QueryBuilder $queryBuilder): QueryBuilder
    {
        $user = $this->getUser();

        return $queryBuilder
            ->innerJoin(UserPosition::class, "userPos", "WITH", sprintf("userPos.application = %s.id", $queryBuilder->getRootAlias()))
            ->andWhere("userPos.user = :user")
            ->setParameter("user", $user->getId()->toString());
    }
}
