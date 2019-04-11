<?php

namespace App\Repository;

use App\Entity\UserAuthenticator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method UserAuthenticator|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserAuthenticator|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserAuthenticator[]    findAll()
 * @method UserAuthenticator[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserAuthenticatorRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, UserAuthenticator::class);
    }

    // /**
    //  * @return UserAuthenticator[] Returns an array of UserAuthenticator objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UserAuthenticator
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
