<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public const PRODUCTS_PER_PAGE = 2;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function findByPage($page = 1, $max = 10)
    {
        $dql = $this->createQueryBuilder("product");
        $dql->orderBy('product.id', 'ASC');

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
            'products' => $paginator,
            'page' => $page,
            'pages' => $pages,
            'limit' => $max,
            'get' => 'page'
        ];
    }

    /*
     * @description Get total products by month
     * 
     * @return array
     */
    public function getTotalProductsByMonth(User $user): array
    {
        $totalProducts = count($this->findBy(['users' => $user]));

        $lastweekProducts = $this->createQueryBuilder('product')
            ->select('count(product.id) as lastweek')
            ->where('product.users = :user')
            ->andWhere('product.createdAt >= :lastWeek')
            ->setParameter('user', $user)
            ->setParameter('lastWeek', new \DateTime('-1 week'))
            ->getQuery()
        ->getSingleResult();
        
        $twoweeksProducts = $this->createQueryBuilder('product')
            ->select('count(product.id) as twoweeks')
            ->where('product.users = :user')
            ->andWhere('product.createdAt >= :twoWeeks')
            ->andWhere('product.createdAt < :lastWeek')
            ->setParameter('user', $user)
            ->setParameter('twoWeeks', new \DateTime('-2 week'))
            ->setParameter('lastWeek', new \DateTime('-1 week'))
            ->getQuery()
        ->getSingleResult();

        return [
            'total' => $totalProducts,
            'lastweek' => $lastweekProducts['lastweek'],
            'twoweeks' => $twoweeksProducts['twoweeks'],
        ];
    }

    //    /**
    //     * @return Product[] Returns an array of Product objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Product
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
