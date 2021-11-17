<?php

namespace App\Modules\UserManagement\Messenger\Commands;

use App\Contracts\UserManagement\Exception\DuplicateTeamMemberException;
use App\Entity\UserManagement\Team;
use App\Modules\Common\Traits\EntityManager;
use App\Modules\Common\Traits\EventBus;
use App\Modules\Common\Traits\Logger;
use App\Modules\UserManagement\Messenger\Events\MemberAddedToTeam;
use Psr\Log\LogLevel;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;

class AddMemberToTeamHandler
{
    use EntityManager;
    use EventBus;
    use Logger;

    public function __invoke(AddMemberToTeam $command): Team
    {
        $team = $command->team;
        $user = $command->user;

        if ($team->getMembers()->contains($user)) {
            throw new DuplicateTeamMemberException($user->getId(), $team->getId());
        }

        $user->addTeam($team);
        $this->em->flush();

        $this->logger->log(
            LogLevel::INFO,
            'User added to team',
            ['userId' => $user->getId(), 'teamId' => $team->getId()]
        );

        $this->eventBus->dispatch(
            new MemberAddedToTeam($team->getId(), $user->getId()),
            [new DispatchAfterCurrentBusStamp()]
        );

        return $team;
    }
}
