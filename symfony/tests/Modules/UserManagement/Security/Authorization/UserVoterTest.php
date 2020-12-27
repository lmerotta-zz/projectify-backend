<?php

namespace App\Tests\Modules\UserManagement\Security\Authorization;

use App\Contracts\Security\Enum\Permission;
use App\Entity\Security\User;
use App\Modules\Security\Authorization\PermissionVoter;
use App\Modules\UserManagement\Security\Authorization\UserVoter;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class UserVoterTest extends TestCase
{
    public function testItGrantsAccessIfUserIsSelfOnVIEW_SELF()
    {
        $id = Uuid::uuid4();
        $user = $this->prophesize(User::class);
        $subject = $this->prophesize(User::class);
        $token = $this->prophesize(TokenInterface::class);

        $user->getId()->shouldBeCalled()->willReturn($id);
        $subject->getId()->shouldBeCalled()->willReturn($id);
        $token->getUser()->shouldBeCalled()->willReturn($user->reveal());

        $voter = new UserVoter();
        $this->assertEquals(PermissionVoter::ACCESS_GRANTED, $voter->vote($token->reveal(), $subject->reveal(), [Permission::USER_VIEW_SELF]));
    }

    public function testItGrantsAccessIfUserIsSelfOnVIEW_LIST()
    {
        $id = Uuid::uuid4();
        $user = $this->prophesize(User::class);
        $subject = $this->prophesize(User::class);
        $token = $this->prophesize(TokenInterface::class);

        $user->getId()->shouldBeCalled()->willReturn($id);
        $subject->getId()->shouldBeCalled()->willReturn($id);
        $token->getUser()->shouldBeCalled()->willReturn($user->reveal());

        $voter = new UserVoter();
        $this->assertEquals(PermissionVoter::ACCESS_GRANTED, $voter->vote($token->reveal(), [$subject->reveal()], [Permission::USER_VIEW_LIST]));
    }

    public function testItDeniesAccessIfUserIsNotSelfOnVIEW_LIST()
    {
        $id = Uuid::uuid4();
        $user = $this->prophesize(User::class);
        $subject = $this->prophesize(User::class);
        $token = $this->prophesize(TokenInterface::class);

        $user->getId()->shouldBeCalled()->willReturn($id);
        $subject->getId()->shouldBeCalled()->willReturn($id, Uuid::uuid4());
        $token->getUser()->shouldBeCalled()->willReturn($user->reveal());

        $voter = new UserVoter();
        $this->assertEquals(PermissionVoter::ACCESS_DENIED, $voter->vote($token->reveal(), [$subject->reveal(), $subject->reveal()], [Permission::USER_VIEW_LIST]));
    }
}
