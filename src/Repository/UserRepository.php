<?php

namespace App\Repository;

use App\DTO\UserSearch;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findPaginated(Request $request, PaginatorInterface $paginator)
    {
      $queryBuilder = $this->createQueryBuilder('p');
      return $paginator->paginate(
          $queryBuilder->getQuery(),
          $request->query->getInt('page',1),
          10
        );
    }

    public function searchUsers(UserSearch $user, PaginatorInterface $paginator, Request $request){
        $qb = $this->createQueryBuilder('u')
            ->andWhere("u.department = :department")
            ->andWhere("u.roles LIKE :roles")
            ->setParameter("roles", sprintf('%%%s%%', $user->roles))
            ->setParameter("department",$user->department);

        if ($user->firstname) {
            $qb->andWhere("u.firstname LIKE :firstname")
                ->setParameter("firstname", sprintf('%%%s%%', $user->firstname));
        }
        if ($user->lastname) {
            $qb->andWhere("u.lastname LIKE :lastname")
                ->setParameter("lastname", sprintf('%%%s%%', $user->lastname));
        }


        return $paginator->paginate(
            $qb->getQuery(),
            $request->query->getInt('page',1),
            10
        );

    }

    // /**
    //  * @return User[] Returns an array of User objects
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
    public function findOneBySomeField($value): ?User
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
