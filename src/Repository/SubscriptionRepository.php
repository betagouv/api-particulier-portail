<?php

namespace App\Repository;

use App\Entity\Api;
use App\Entity\ApiKey;
use App\Entity\Application;
use App\Entity\Subscription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Subscription|null find($id, $lockMode = null, $lockVersion = null)
 * @method Subscription|null findOneBy(array $criteria, array $orderBy = null)
 * @method Subscription[]    findAll()
 * @method Subscription[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubscriptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subscription::class);
    }

    public function activeSubscriptionExistsForApiKeyHashAndPath(string $apiKeyHash, string $path)
    {
        $qb = $this->createQueryBuilder("s")
            ->select("count(s.id)")
            ->innerJoin(Api::class, "api", "WITH", "s.api = api.id")
            ->where("api.path = :path")
            ->innerJoin(Application::class, "app", "WITH", "app.id = s.application")
            ->innerJoin(ApiKey::class, "apiKey", "WITH", "apiKey.application = app.id")
            ->andWhere("apiKey.hash = :hash")
            ->andWhere("apiKey.active = true")
            ->andWhere("s.active = true")
            ->setParameter("path", $path)
            ->setParameter("hash", $apiKeyHash);

        return $qb->getQuery()->getSingleScalarResult() > 0;
    }

    // /**
    //  * @return Subscription[] Returns an array of Subscription objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Subscription
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
