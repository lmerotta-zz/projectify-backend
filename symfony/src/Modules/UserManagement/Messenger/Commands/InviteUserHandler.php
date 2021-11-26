<?php

namespace App\Modules\UserManagement\Messenger\Commands;

use App\Contracts\UserManagement\Exception\UserAlreadyRegisteredException;
use App\Entity\UserManagement\Invitation;
use App\Modules\Common\Traits\EntityManager;
use App\Modules\Common\Traits\UserRepository;
use App\Repository\UserManagement\InvitationRepository;
use Ramsey\Uuid\Uuid;
use Symfony\Contracts\Service\Attribute\Required;

class InviteUserHandler
{
    use UserRepository;
    use EntityManager;

    private InvitationRepository $repository;

    public function __invoke(InviteUser $command): Invitation
    {
        $email = $command->email;

        $user = $this->userRepository->findOneBy(['email' => $email]);
        if (!is_null($user)) {
            throw new UserAlreadyRegisteredException($user->getId());
        }

        $invitation = $this->repository->getNonExpiredInvitationFor($email);
        if (is_null($invitation)) {
            $invitation = Invitation::create(Uuid::uuid4(), $email);
        }

        $this->em->persist($invitation);
        $this->em->flush();

        return $invitation;
    }

    #[Required]
    public function setInvitationRepository(InvitationRepository $repository): void
    {
        $this->repository = $repository;
    }
}
