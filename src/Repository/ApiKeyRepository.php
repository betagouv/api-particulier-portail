<?php

namespace App\Repository;

use App\Entity\ApiKey;
use App\Entity\Application;
use App\Entity\User;
use App\Entity\UserPosition;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ApiKey|null find($id, $lockMode = null, $lockVersion = null)
 * @method ApiKey|null findOneBy(array $criteria, array $orderBy = null)
 * @method ApiKey[]    findAll()
 * @method ApiKey[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApiKeyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ApiKey::class);
    }

    public function findRelatedToUser(User $user)
    {
        return $this->createQueryBuilder("apiKey")
            ->select("apiKey")
            ->innerJoin(Application::class, "app", "WITH", "app.id = apiKey.application")
            ->innerJoin(UserPosition::class, "userPos", "WITH", "userPos.user = :user")
            ->setParameter("user", $user->getId())
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return ApiKey[] Returns an array of ApiKey objects
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
    public function findOneBySomeField($value): ?ApiKey
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
