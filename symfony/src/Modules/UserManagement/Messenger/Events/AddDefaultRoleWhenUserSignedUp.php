<?php

namespace App\Modules\UserManagement\Messenger\Events;

use App\Repository\Security\RoleRepository;
use App\Repository\Security\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class AddDefaultRoleWhenUserSignedUp
{
    private const DEFAULT_ROLE = 'ROLE_USER';

    private RoleRepository $roleRepository;
    private UserRepository $userRepository;
    private EntityManagerInterface $em;

    public function __invoke(UserSignedUp $event): void
    {
        $user = $this->userRepository->find($event->getId());
        $role = $this->roleRepository->find(self::DEFAULT_ROLE);

        $user->addRole($role);

        $this->em->flush();
    }

    /**
     * @required
     */
    public function setRoleRepository(RoleRepository $roleRepository): void
    {
        $this->roleRepository = $roleRepository;
    }

    /**
     * @required
     */
    public function setUserRepository(UserRepository $userRepository): void
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @required
     */
    public function setEm(EntityManagerInterface $em): void
    {
        $this->em = $em;
    }
}
