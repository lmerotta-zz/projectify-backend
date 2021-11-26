<?php

namespace App\Repository\UserManagement;

use App\Entity\UserManagement\PostInvitationAction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PostInvitationAction|null find($id, $lockMode = null, $lockVersion = null)
 * @method PostInvitationAction|null findOneBy(array $criteria, array $orderBy = null)
 * @method PostInvitationAction[]    findAll()
 * @method PostInvitationAction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostInvitationActionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PostInvitationAction::class);
    }
}
