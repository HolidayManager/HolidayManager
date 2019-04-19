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
        /*
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
        )*/

        $result = $qb->orWhere("h.startDate<=:start AND h.endDate>=:start AND h.status=:status",
                                "h.startDate>=:start AND h.endDate<=:end AND h.status=:status",
                                "h.startDate<=:end AND h.endDate>=:end AND h.status=:status")
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->setParameter('status','a')
            ->getQuery()->getResult();



        return $result;
    }

    public function getPending($departmentid){
        $qb = $this->createQueryBuilder('h');

        return    $qb->leftJoin('h.user','u')
            ->andWhere($qb->expr()->eq("h.status",":status"),
                        $qb->expr()->eq("u.department",":department"))
            ->setParameter("department",$departmentid)
            ->setParameter("status",'p')
            ->orderBy("h.dateRequest","DESC")
            ->getQuery()
            ->getResult();
    }

    public function spentCurrentYear($userid,$endDate){
        $qb = $this->createQueryBuilder('u');

        return $qb->leftJoin("h.user" , "u")
                ->andWhere("h.status",":status",
                            "h.user=:user",
                            "h.endDate<=:end")
            ->setParameter("status","a")
            ->setParameter("end",date("Y-m-d"))
            ->setParameter("user",$userid)
            ->getQuery()->getResult();
    }
    public function toSpentYear($userid,$endDate){
        $qb = $this->createQueryBuilder('u');

        return $qb->leftJoin("h.user" , "u")
            ->andWhere("h.status",":status",
                "h.user=:user",
                "h.startDate>=:now",
                "h.startDate>=:firstDayOfYear")
            ->setParameter("status","a")
            ->setParameter("start",date("Y-m-d"))
            ->setParameter("user",$userid)
            ->setParameter("now",date("Y-m-d"))
            ->getQuery()->getResult();
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
