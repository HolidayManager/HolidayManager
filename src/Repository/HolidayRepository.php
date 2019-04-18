<?php

namespace App\Repository;

use App\Entity\Holiday;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Holiday|null find($id, $lockMode = null, $lockVersion = null)
 * @method Holiday|null findOneBy(array $criteria, array $orderBy = null)
 * @method Holiday[]    findAll()
 * @method Holiday[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HolidayRepository extends ServiceEntityRepository
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(RegistryInterface $registry, LoggerInterface $logger)
    {
        parent::__construct($registry, Holiday::class);
        $this->logger = $logger;
    }

    public function getHolidayIn($start, $end)
    {


        $qb = $this->createQueryBuilder('h');

        $result = $qb->where(
            $qb->expr()->orX(
                $qb->expr()->andX(
                    $qb->expr()->gte('h.startDate', ':start'),
                    $qb->expr()->gte('h.endDate', ':end'),
                    $qb->expr()->eq('h.status',':status')
                ),
                $qb->expr()->andX(
                    $qb->expr()->lte('h.startDate', ':start'),
                    $qb->expr()->gte('h.endDate', ':start'),
                    $qb->expr()->eq('h.status',':status')
                ),
                $qb->expr()->andX(
                    $qb->expr()->lte('h.startDate', ':end'),
                    $qb->expr()->gte('h.endDate', ':end'),
                    $qb->expr()->eq('h.status',':status')
                )
            )
        )->setParameter('start', $start)
            ->setParameter('end', $end)
            ->setParameter('status','a')
            ->getQuery()->getResult();



        return $result;
    }

    // /**
    //  * @return Holiday[] Returns an array of Holiday objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('h.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Holiday
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
