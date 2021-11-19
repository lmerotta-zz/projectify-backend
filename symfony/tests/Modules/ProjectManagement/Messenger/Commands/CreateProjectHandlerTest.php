<?php

namespace App\Tests\Modules\ProjectManagement\Messenger\Commands;

use App\Entity\ProjectManagement\Project;
use App\Modules\Common\Bus\EventBus;
use App\Modules\ProjectManagement\Messenger\Commands\CreateProject;
use App\Modules\ProjectManagement\Messenger\Commands\CreateProjectHandler;
use App\Modules\ProjectManagement\Messenger\Events\ProjectCreated;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;

class CreateProjectHandlerTest extends TestCase
{
    use ProphecyTrait;

    public function testItCreatesTheProject()
    {
        $em = $this->prophesize(EntityManagerInterface::class);
        $eventBus = $this->prophesize(EventBus::class);

        $em->persist(Argument::type(Project::class))->shouldBeCalled();
        $em->flush()->shouldBeCalled();

        $eventBus->dispatch(Argument::type(ProjectCreated::class), [new DispatchAfterCurrentBusStamp()])->shouldBeCalled();

        $command = new CreateProject('Test project', null);
        $handler = new CreateProjectHandler();
        $handler->setEntityManager($em->reveal());
        $handler->setEventBus($eventBus->reveal());

        $handler($command);
    }
}
