<?php

namespace App\Repository;

use App\DTO\searchHoliday;
use App\Entity\Holiday;
use App\Entity\User;
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

    public function spentCurrentYear(User $user){


        return $this->createQueryBuilder('h')
                ->andWhere("h.status=:status",
                            "h.user=:user",
                            "h.endDate<=:end",
                            "h.startDate>=:start")
            ->setParameter("status","a")
            ->setParameter("end",date("Y-m-d"))
            ->setParameter("user",$user->getId())
            ->setParameter("start",date("Y-01-01"))
            ->getQuery()->getResult();
    }



    public function toSpentYear(User $user){

        return $this->createQueryBuilder('h')
            ->andWhere("h.status=:status",
                "h.user=:user",
                "h.endDate<=:end",
                "h.startDate>=:start")
            ->setParameter("status","a")
            ->setParameter("end",date("Y-12-31"))
            ->setParameter("user",$user->getId())
            ->setParameter("start",date("Y-m-d"))
            ->getQuery()->getResult();

    }


    public function lastRequested($user){
        return $this->createQueryBuilder('h')
            ->leftJoin("h.user","u")
            ->andWhere("h.user=:user")
            ->orderBy("h.dateRequest","DESC")
            ->setParameter("user",$user)
            ->setMaxResults(1)
            ->getQuery()->getOneOrNullResult();
    }



    public function searchedHoliday(searchHoliday $searchedHoliday)
    {
        $qb = $this->createQueryBuilder('h');
        $qb->leftJoin('h.user','u')
            ->orWhere("h.startDate<=:startDate AND h.endDate>=:startDate AND h.status=:status",
                "h.startDate>=:startDate AND h.endDate<=:endDate AND h.status=:status",
                "h.startDate<=:endDate AND h.endDate>=:endDate AND h.status=:status"

                );
        if(!empty($searchedHoliday->firstname)){
            $qb->andWhere("u.firstname LIKE :firstname")
            ->setParameter('firstname',sprintf('%%%s%%', $searchedHoliday->firstname));
        }

        if(!empty($searchedHoliday->lastname)){
            $qb->andWhere("u.lastname LIKE :lastname")
                ->setParameter('lastname',sprintf('%%%s%%', $searchedHoliday->lastname));
        }
        if(!empty($searchedHoliday->department)){
            $qb->andWhere("u.department LIKE :department")
                ->setParameter('department',sprintf('%%%s%%', $searchedHoliday->department));
        }
        if(!empty($searchedHoliday->role)){
            $qb->andWhere("u.roles LIKE :roles")
                ->setParameter('roles',sprintf('%%%s%%', $searchedHoliday->role));
        }
        return $qb
            ->setParameter('startDate',$searchedHoliday->startDate->format("Y-m-d"))
            ->setParameter('endDate',$searchedHoliday->endDate->format("Y-m-d"))
            ->setParameter('status','a')
            ->getQuery()
            ->getResult();
    }

    public function tokenHoliday(\DateTime $startDate, \DateTime $endDate, User $user){
        $qb = $this->createQueryBuilder('h');

        return    $qb->andWhere($qb->expr()->orX("h.startDate<=:startDate AND h.endDate>=:startDate",
            "h.startDate>=:startDate AND h.endDate<=:endDate",
            "h.startDate<=:endDate AND h.endDate>=:endDate"),
                        "h.user=:user",
                        "h.status!=:status")
            ->setParameter("startDate",$startDate)
            ->setParameter("endDate",$endDate)
            ->setParameter("user",$user)
            ->setParameter("status","r")
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
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
