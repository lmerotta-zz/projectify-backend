<?php

namespace App\Tests\Modules\UserManagement\Security\Authorization;

use App\Contracts\Security\Enum\Permission;
use App\Entity\Security\User;
use App\Entity\UserManagement\Team;
use App\Modules\Security\Authorization\PermissionVoter;
use App\Modules\UserManagement\Security\Authorization\UserVoter;
use App\Tests\Helpers\ReflectionTrait;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Prophecy\PhpUnit\ProphecyTrait;

class UserVoterTest extends TestCase
{

    use ProphecyTrait;
    use ReflectionTrait;

    public function testItGrantsAccessIfUserIsSelfVIEW()
    {
        $id = Uuid::uuid4();
        $user = $this->prophesize(User::class);
        $subject = $this->prophesize(User::class);
        $token = $this->prophesize(TokenInterface::class);

        $user->getId()->shouldBeCalled()->willReturn($id);
        $subject->getId()->shouldBeCalled()->willReturn($id);
        $token->getUser()->shouldBeCalled()->willReturn($user->reveal());

        $voter = new UserVoter();
        $this->assertEquals(PermissionVoter::ACCESS_GRANTED, $voter->vote($token->reveal(), $subject->reveal(), [Permission::USER_VIEW]));
    }

    public function testItGrantsAccessIfUserIsMemberOfSameTeamsAsSelfOnVIEW()
    {
        $id1 = Uuid::uuid4();
        $id2 = Uuid::uuid4();
        $user = User::create($id1, 'first', 'last', 'dummy', 'first@test.com');
        $subject = User::create($id2, 'test', 'test', 'dummy', 'test@test.com');
        $teamsOwner = User::create(Uuid::uuid4(), 'own', 'own', 'own', 'own@test.com');
        $token = $this->prophesize(TokenInterface::class);
        $commonTeamId = Uuid::uuid4();
        $commonTeam = Team::create($commonTeamId, 'common');
        $this->setFieldValue($commonTeam, 'owner', $teamsOwner);

        for ($i = 0; $i < 10; $i++) {
            $teamUser = Team::create(Uuid::uuid4(), 'team user '.$i);
            $teamSubject = Team::create(Uuid::uuid4(), 'team subject '.$i);
            $this->setFieldValue($teamSubject, 'owner', $teamsOwner);

            $user->addTeam($teamUser);
            $subject->addTeam($teamSubject);
        }

        $user->addTeam($commonTeam);
        $subject->addTeam($commonTeam);

        $token->getUser()->shouldBeCalled()->willReturn($user);

        $voter = new UserVoter();
        $this->assertEquals(PermissionVoter::ACCESS_GRANTED, $voter->vote($token->reveal(), $subject, [Permission::USER_VIEW]));
    }

    public function testItGrantsAccessIfUserIsOfTeamOwnedBySelfOnVIEW()
    {
        $id1 = Uuid::uuid4();
        $id2 = Uuid::uuid4();
        $user = User::create($id1, 'first', 'last', 'dummy', 'first@test.com');
        $subject = User::create($id2, 'test', 'test', 'dummy', 'test@test.com');
        $teamsOwner = User::create(Uuid::uuid4(), 'own', 'own', 'own', 'own@test.com');
        $token = $this->prophesize(TokenInterface::class);

        for ($i = 0; $i < 10; $i++) {
            $teamSubject = Team::create(Uuid::uuid4(), 'team subject '.$i);
            $this->setFieldValue($teamSubject, 'owner', $teamsOwner);

            $subject->addTeam($teamSubject);
        }

        $ownedBySelf = Team::create(Uuid::uuid4(), 'owned by self');
        $this->setFieldValue($ownedBySelf, 'owner', $user);
        $subject->addTeam($ownedBySelf);

        $token->getUser()->shouldBeCalled()->willReturn($user);

        $voter = new UserVoter();
        $this->assertEquals(PermissionVoter::ACCESS_GRANTED, $voter->vote($token->reveal(), $subject, [Permission::USER_VIEW]));
    }

    public function testItGrantsAccessIfUserIsSelfOnEDIT()
    {
        $id = Uuid::uuid4();
        $user = $this->prophesize(User::class);
        $subject = $this->prophesize(User::class);
        $token = $this->prophesize(TokenInterface::class);

        $user->getId()->shouldBeCalled()->willReturn($id);
        $subject->getId()->shouldBeCalled()->willReturn($id);
        $token->getUser()->shouldBeCalled()->willReturn($user->reveal());

        $voter = new UserVoter();
        $this->assertEquals(PermissionVoter::ACCESS_GRANTED, $voter->vote($token->reveal(), $subject->reveal(), [Permission::USER_EDIT]));
    }
}
