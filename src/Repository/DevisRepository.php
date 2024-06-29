<?php

namespace App\Repository;

use App\Entity\Devis;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @method Devis|null find($id, $lockMode = null, $lockVersion = null)
 * @method Devis|null findOneBy(array $criteria, array $orderBy = null)
 * @method Devis[]    findAll()
 * @method Devis[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DevisRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Devis::class);
    }

    /**
     * Retrieve the last chrono set to a Devis by the logged User.
     *
     * @param User $user The current logged User
     */
    public function findLastChrono(User $user): ?string
    {
        $query = $this->createQueryBuilder('d')
            ->select('d.chrono')
            ->join('d.customer', 'c')
            ->where('c.owner = :user')
            ->setParameter('user', $user)
            ->orderBy('d.chrono', 'DESC')
            ->setMaxResults(1)
            ->getQuery();

        try {
            return $query->getSingleScalarResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    public function findByPage($page = 1, $max = 10)
    {
        $dql = $this->createQueryBuilder("devis");
        $dql->orderBy('devis.id', 'ASC');

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
            'devis' => $paginator,
            'total' => $paginator->count(),
            'page' => $page,
            'pages' => $pages,
            'limit' => $max,
            'get' => 'page'
        ];
    }

    // /**
    //  * @return Devis[] Returns an array of Devis objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Devis
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
