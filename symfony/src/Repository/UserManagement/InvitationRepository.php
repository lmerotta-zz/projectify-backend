<?php

namespace App\Repository\UserManagement;

use App\Entity\UserManagement\Invitation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Invitation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Invitation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Invitation[]    findAll()
 * @method Invitation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InvitationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Invitation::class);
    }

    public function getNonExpiredInvitationFor(string $email): ?Invitation
    {
        return $this->createQueryBuilder('invitation')
            ->andWhere('invitation.email LIKE :email')
            ->andWhere('invitation.expirationDate < :now')
            ->setParameters(['email' => $email, 'now' => new \DateTimeImmutable()])
            ->getQuery()->getOneOrNullResult();
    }
}
