<?php

namespace App\Modules\UserManagement\Messenger\Commands;

use App\Entity\UserManagement\Team;
use App\Modules\Common\Traits\EntityManager;
use App\Modules\Common\Traits\EventBus;
use App\Modules\UserManagement\Messenger\Events\TeamCreated;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;

class CreateTeamHandler
{
    use EntityManager;
    use EventBus;

    public function __invoke(CreateTeam $command): Team
    {
        $team = Team::create(Uuid::uuid4(), $command->name);
        $this->em->persist($team);
        $this->em->flush();

        $this->eventBus->dispatch(new TeamCreated($team->getId()), [new DispatchAfterCurrentBusStamp()]);

        return $team;
    }
}