<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Invoice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Doctrine\ORM\Query\ResultSetMapping;

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

    /*
     * @description Retrieve the total invoices by month for the last year.
     * 
     * @return array
     */
    public function getTotalInvoicesByMonth(User $user): array
    {
        $totalInvoices = count($this->findBy(['users' => $user]));

        $lastweekInvoices = $this->createQueryBuilder('invoices')
            ->select('count(invoices.id) as lastweek')
            ->where('invoices.users = :user')
            ->andWhere('invoices.createdAt >= :lastWeek')
            ->setParameter('user', $user)
            ->setParameter('lastWeek', new \DateTime('-1 week'))
            ->getQuery()
        ->getSingleResult();
        
        $twoweeksInvoices = $this->createQueryBuilder('invoices')
            ->select('count(invoices.id) as twoweeks')
            ->where('invoices.users = :user')
            ->andWhere('invoices.createdAt >= :twoWeeks')
            ->andWhere('invoices.createdAt < :lastWeek')
            ->setParameter('user', $user)
            ->setParameter('twoWeeks', new \DateTime('-2 week'))
            ->setParameter('lastWeek', new \DateTime('-1 week'))
            ->getQuery()
        ->getSingleResult();

        return [
            'total' => $totalInvoices,
            'lastweek' => $lastweekInvoices['lastweek'],
            'twoweeks' => $twoweeksInvoices['twoweeks'],
        ];
    }

    /*
     * @description Retrieve the total amount paid by month for the last year.
     * 
     * @return array
     */
    public function getTotalPaidAmountByMonth(User $user): array
    {
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('month', 'month');
        $rsm->addScalarResult('total_amount_paid', 'totalAmountPaid');

        $sql = "
            SELECT 
                DATE_TRUNC('month', inv.paid_at) AS month,
                SUM(ins.quantity * ins.unit_price) AS total_amount_paid
            FROM 
                invoices inv
            JOIN 
                invoices_services ins ON inv.id = ins.invoice_id
            WHERE 
                inv.paid_at IS NOT NULL
                AND inv.paid_at >= NOW() - INTERVAL '1 year'
                AND inv.users_id = :user
                AND inv.status = 'PAID'
            GROUP BY 
                month
            ORDER BY 
                month
        ";

        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $query->setParameter('user', $user);

        return $query->getResult();
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
