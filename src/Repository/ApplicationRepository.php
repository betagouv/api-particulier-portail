<?php

namespace App\Repository;

use App\Entity\Api;
use App\Entity\ApiKey;
use App\Entity\Application;
use App\Entity\Subscription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Application|null find($id, $lockMode = null, $lockVersion = null)
 * @method Application|null findOneBy(array $criteria, array $orderBy = null)
 * @method Application[]    findAll()
 * @method Application[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApplicationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Application::class);
    }

    public function findOneByApiKeyHashAndApiPath(string $apiKeyHash, string $apiPath)
    {
        $qb = $this->createQueryBuilder("app")
            ->select("app")
            ->innerJoin(Subscription::class, "s", "WITH", "s.application = app.id")
            ->innerJoin(Api::class, "api", "WITH", "s.api = api.id")
            ->where("api.path = :path")
            ->innerJoin(ApiKey::class, "apiKey", "WITH", "apiKey.application = app.id")
            ->andWhere("apiKey.hash = :hash")
            ->andWhere("apiKey.active = true")
            ->andWhere("s.active = true")
            ->setParameter("path", $apiPath)
            ->setParameter("hash", $apiKeyHash);

        return $qb->getQuery()->getOneOrNullResult();
    }

    // /**
    //  * @return Application[] Returns an array of Application objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Application
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
