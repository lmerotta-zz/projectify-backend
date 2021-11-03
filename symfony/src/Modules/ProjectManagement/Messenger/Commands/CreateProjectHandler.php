<?php

namespace App\Modules\ProjectManagement\Messenger\Commands;

use App\Entity\ProjectManagement\Project;
use App\Modules\Common\Traits\EntityManager;
use App\Modules\Common\Traits\EventBus;
use App\Modules\ProjectManagement\Messenger\Events\ProjectCreated;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;

class CreateProjectHandler
{
    use EntityManager;
    use EventBus;

    public function __invoke(CreateProject $command): Project
    {
        $project = Project::create(Uuid::uuid4(), $command->name, $command->description);

        $this->em->persist($project);
        $this->em->flush();

        $this->eventBus->dispatch(new ProjectCreated($project->getId()), [new DispatchAfterCurrentBusStamp()]);

        return $project;
    }
}
