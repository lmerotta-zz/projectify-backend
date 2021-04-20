<?php

namespace App\Modules\UserManagement\Messenger\Events;

use App\Modules\Common\Traits\EntityManager;
use App\Modules\Common\Traits\Logger;
use App\Modules\Common\Traits\UserRepository;
use App\Repository\Security\RoleRepository;
use Psr\Log\LogLevel;
use Symfony\Contracts\Service\Attribute\Required;

class AddDefaultRoleWhenUserSignedUpHandler
{
    use Logger;
    use UserRepository;
    use EntityManager;
    private const DEFAULT_ROLE = 'ROLE_USER';

    private RoleRepository $roleRepository;

    public function __invoke(UserSignedUp $event): void
    {
        $this->logger->log(
            LogLevel::INFO,
            'Adding default role to user',
            ['user' => $event->getId()->toString(), 'role' => self::DEFAULT_ROLE]
        );
        $user = $this->userRepository->find($event->getId());
        $role = $this->roleRepository->find(self::DEFAULT_ROLE);

        $user->addRole($role);

        $this->em->flush();
    }

    #[Required]
    public function setRoleRepository(RoleRepository $roleRepository): void
    {
        $this->roleRepository = $roleRepository;
    }
}
