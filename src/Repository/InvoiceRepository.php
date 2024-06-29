<?php

namespace App\Repository;

use App\Entity\Invoice;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @method Invoice|null find($id, $lockMode = null, $lockVersion = null)
 * @method Invoice|null findOneBy(array $criteria, array $orderBy = null)
 * @method Invoice[]    findAll()
 * @method Invoice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InvoiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Invoice::class);
    }

    /**
     * Retrieve the last chrono set to an Invoice by the logged User.
     *
     * @param User $user The current logged User
     */
    public function findLastChrono(User $user): ?string
    {
        $query = $this->createQueryBuilder('i')
            ->select('i.chrono')
            ->join('i.customer', 'c')
            ->where('c.owner = :user')
            ->setParameter('user', $user)
            ->orderBy('i.chrono', 'DESC')
            ->setMaxResults(1)
            ->getQuery();

        try {
            return $query->getSingleScalarResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    /*
     * Retrieve all the invoices for the logged User.
     * 
     * @param User $user The current logged User
     */
    public function findByPage($page = 1, $max = 10): array
    {
        $dql = $this->createQueryBuilder("invoices");
        $dql->orderBy('invoices.id', 'ASC');

        $firstResult = ($page - 1) * $max;

        $query = $dql->getQuery();
        $query->setFirstResult($firstResult);
        $query->setMaxResults($max);

        $paginator = new Paginator($query);

        if(($paginator->count() <= $firstResult) && $page != 1) {
            throw new NotFoundHttpException('Page not found');
        }

        $pages = ceil($paginator->count() / $paginator->getQuery()->getMaxResults());

        return [
            'invoices' => $paginator,
            'total' => $paginator->count(),
            'page' => $page,
            'pages' => $pages,
            'limit' => $max,
            'get' => 'page'
        ];
    }

    // /**
    //  * @return Invoice[] Returns an array of Invoice objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Invoice
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
