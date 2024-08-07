<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Customer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @method Customer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Customer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Customer[]    findAll()
 * @method Customer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Customer::class);
    }

    public function findByPage($page = 1, $max = 10)
    {
        $dql = $this->createQueryBuilder("customers");
        $dql->orderBy('customers.id', 'ASC');

        $firstResult = ($page - 1) * $max;

        $query = $dql->getQuery();
        $query->setFirstResult($firstResult);
        $query->setMaxResults($max);

        $paginator = new Paginator($query);

        if(($paginator->count() <=  $firstResult) && $page != 1) {
            throw new NotFoundHttpException('Page not found');
        }

        $pages = ceil($paginator->count() / $paginator->getQuery()->getMaxResults());

        return [
            'customers' => $paginator,
            'page' => $page,
            'pages' => $pages,
            'limit' => $max,
            'get' => 'page'
        ];
    }

    public function getTotalCustomersByMonth(User $user): array
    {
        $totalCus = count($this->findBy(['owner' => $user]));

        $lastweekCus = $this->createQueryBuilder('customer')
            ->select('count(customer.id) as lastweek')
            ->where('customer.owner = :owner')
            ->andWhere('customer.createdAt >= :lastWeek')
            ->setParameter('owner', $user)
            ->setParameter('lastWeek', new \DateTime('-1 week'))
            ->getQuery()
        ->getSingleResult();

        $twoweeksCus = $this->createQueryBuilder('customer')
            ->select('count(customer.id) as twoweeks')
            ->where('customer.owner = :owner')
            ->andWhere('customer.createdAt >= :twoWeeks')
            ->andWhere('customer.createdAt < :lastWeek')
            ->setParameter('owner', $user)
            ->setParameter('twoWeeks', new \DateTime('-2 week'))
            ->setParameter('lastWeek', new \DateTime('-1 week'))
            ->getQuery()
        ->getSingleResult();

        // return $dql->getQuery()->getResult();
        return [
            'total' => $totalCus,
            'lastweek' => $lastweekCus['lastweek'],
            'twoweeks' => $twoweeksCus['twoweeks'],
        ];
    }

    // /**
    //  * @return Customer[] Returns an array of Customer objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Customer
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
