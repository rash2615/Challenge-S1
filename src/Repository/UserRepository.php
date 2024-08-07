<?php

namespace App\Repository;

use App\Entity\User;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword, false);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    /**
     * Delete all the unconfirmed accounts of the database.
     */
    public function clearUnconfirmedAccounts(DateTime $dateTime): array
    {
        $unconfirmedAccounts = $this->createQueryBuilder('u')
            ->where('u.createdAt < :dateTime')
            ->setParameter('dateTime', $dateTime)
            ->andWhere('u.confirmedAt IS NULL')
            ->getQuery()
            ->getResult();

        if (!empty($unconfirmedAccounts)) {
            foreach ($unconfirmedAccounts as $unconfirmedAccount) {
                $this->_em->remove($unconfirmedAccount);
                $this->_em->flush();
            }

            return [true, $unconfirmedAccounts];
        }

        return [false, []];
    }
}
