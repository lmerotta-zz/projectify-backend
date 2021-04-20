<?php

namespace App\Modules\UserManagement\Messenger\Commands;

use App\Contracts\UserManagement\Exception\UserAlreadyOnboardedException;
use App\Contracts\UserManagement\Exception\UserNotFoundException;
use App\Entity\Security\User;
use App\Modules\Common\Traits\UserRepository;
use Symfony\Component\Workflow\WorkflowInterface;
use Symfony\Contracts\Service\Attribute\Required;

class OnboardUserHandler
{
    use UserRepository;

    private WorkflowInterface $stateMachine;

    public function __invoke(OnboardUser $command): User
    {
        $user = $this->userRepository->find($command->id);

        if (!$user) {
            throw new UserNotFoundException($command->id);
        }

        if (!$this->stateMachine->can($user, 'onboard')) {
            throw new UserAlreadyOnboardedException($command->id);
        }

        $user->profilePictureFile = $command->profilePicture;
        $user->setFirstName($command->firstName)
            ->setLastName($command->lastName);

        $this->stateMachine->apply($user, 'onboard');

        return $user;
    }

    #[Required]
    public function setWorkflow(WorkflowInterface $userJourneyStateMachine): void
    {
        $this->stateMachine = $userJourneyStateMachine;
    }
}
