<?php

namespace App\EventSubscriber\Admin;

use App\Entity\ApiKey;
use App\Entity\Application;
use App\Entity\UserPosition;
use Doctrine\ORM\QueryBuilder;

class ApiKeyAdminQueryBuilderSubscriber extends AbstractAdminQueryBuilderSubscriber
{
    protected function getTargetEntity(): string
    {
        return ApiKey::class;
    }

    protected function getNewQueryBuilder(QueryBuilder $queryBuilder): QueryBuilder
    {
        $user = $this->getUser();

        return $queryBuilder
            ->innerJoin(Application::class, "app", "WITH", sprintf("app.id = %s.application", $queryBuilder->getRootAlias()))
            ->innerJoin(UserPosition::class, "userPos", "WITH", "userPos.application = app.id")
            ->andWhere("userPos.user = :user")
            ->setParameter("user", $user->getId()->toString());
    }
}
