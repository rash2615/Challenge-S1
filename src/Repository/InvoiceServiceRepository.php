<?php

namespace App\Repository;

use App\Entity\InvoiceService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method InvoiceService|null find($id, $lockMode = null, $lockVersion = null)
 * @method InvoiceService|null findOneBy(array $criteria, array $orderBy = null)
 * @method InvoiceService[]    findAll()
 * @method InvoiceService[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InvoiceServiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InvoiceService::class);
    }

    // /**
    //  * @return InvoiceService[] Returns an array of InvoiceService objects
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
    public function findOneBySomeField($value): ?InvoiceService
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
