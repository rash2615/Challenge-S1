<?php

namespace App\Repository;

use App\Entity\Devis;
use App\Entity\Invoice;
use App\Entity\InvoicesToken;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


class InvoicesTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InvoicesToken::class);
    }

    public function findByToken(string $token, Invoice $invoice, Devis $devis): ?InvoicesToken
    {
        if ($invoice) {
            return $this->createQueryBuilder('it')
                ->where('it.token = :token')
                ->andWhere('it.invoice = :invoice')
                ->setParameter('token', $token)
                ->setParameter('invoice', $invoice)
                ->getQuery()
                ->getOneOrNullResult();
        }

        if ($devis) {
            return $this->createQueryBuilder('it')
                ->where('it.token = :token')
                ->andWhere('it.devis = :devis')
                ->setParameter('token', $token)
                ->setParameter('devis', $devis)
                ->getQuery()
                ->getOneOrNullResult();
        }
    }

    // Add your custom repository methods here
}
