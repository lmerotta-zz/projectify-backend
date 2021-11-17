<?php

namespace App\Tests\Modules\UserManagement\Messenger\Commands;

use App\Contracts\UserManagement\Exception\DuplicateTeamMemberException;
use App\Entity\Security\User;
use App\Entity\UserManagement\Team;
use App\Modules\Common\Bus\EventBus;
use App\Modules\UserManagement\Messenger\Commands\AddMemberToTeam;
use App\Modules\UserManagement\Messenger\Commands\AddMemberToTeamHandler;
use App\Modules\UserManagement\Messenger\Events\MemberAddedToTeam;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;

class AddMemberToTeamHandlerTest extends TestCase
{
    use ProphecyTrait;

    public function testItCanAddAMemberToTheTeam(): void
    {
        $teamId = Uuid::uuid4();
        $userId = Uuid::uuid4();
        $team = Team::create($teamId, 'name');
        $user = User::create($userId, 'first', 'last', 'pass', 'test@test.com');

        $em = $this->prophesize(EntityManagerInterface::class);
        $eventBus = $this->prophesize(EventBus::class);
        $logger = $this->prophesize(LoggerInterface::class);

        $em->flush()->shouldBeCalledOnce();
        $eventBus->dispatch(new MemberAddedToTeam($teamId, $userId), [new DispatchAfterCurrentBusStamp()])->shouldBeCalledOnce();

        $handler = new AddMemberToTeamHandler();
        $handler->setEntityManager($em->reveal());
        $handler->setEventBus($eventBus->reveal());
        $handler->setLogger($logger->reveal());

        $command = new AddMemberToTeam($team, $user);
        $handler($command);

        $this->assertContains($user, $team->getMembers());
    }

    public function testItThrowsIfWeTryToAddTheSameMemberTwice(): void
    {
        $this->expectException(DuplicateTeamMemberException::class);

        $teamId = Uuid::uuid4();
        $userId = Uuid::uuid4();
        $team = Team::create($teamId, 'name');
        $user = User::create($userId, 'first', 'last', 'pass', 'test@test.com');

        $team->addMember($user);

        $em = $this->prophesize(EntityManagerInterface::class);
        $eventBus = $this->prophesize(EventBus::class);
        $logger = $this->prophesize(LoggerInterface::class);

        $em->flush()->shouldNotBeCalled();
        $eventBus->dispatch(new MemberAddedToTeam($teamId, $userId), [new DispatchAfterCurrentBusStamp()])->shouldNotBeCalled();

        $handler = new AddMemberToTeamHandler();
        $handler->setEntityManager($em->reveal());
        $handler->setEventBus($eventBus->reveal());
        $handler->setLogger($logger->reveal());

        $command = new AddMemberToTeam($team, $user);
        $handler($command);
    }
}
