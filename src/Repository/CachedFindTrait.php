<?php

namespace App\Repository;

trait CachedFindTrait
{
    public function find($id, $lockMode = NULL, $lockVersion = NULL)
    {
        $query = $this->createQueryBuilder("entity")
            ->select("entity")
            ->where("entity.id = :id")
            ->setParameter("id", $id)
            ->getQuery();
        $query->useResultCache(true);

        return $query->getSingleResult();
    }
}
