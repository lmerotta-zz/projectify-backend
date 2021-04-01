<?php

namespace App\Tests\Modules\Security\Authorization;

use App\Contracts\Security\Enum\Permission;
use App\Entity\Security\User;
use App\Modules\Security\Authorization\PermissionVoter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Prophecy\PhpUnit\ProphecyTrait;

class PermissionVoterTest extends TestCase
{
    use ProphecyTrait;

    public function testItReturnsAbstainIfNotAUser()
    {
        $token = $this->prophesize(TokenInterface::class);
        $token->getUser()->shouldBeCalled()->willReturn('anon.');

        $voter = new PermissionVoter();
        $this->assertEquals(PermissionVoter::ACCESS_ABSTAIN, $voter->vote($token->reveal(), null, ['test']));
    }

    public function testItAbstainsIfAttributeIsNotAPermission()
    {
        $user = $this->prophesize(User::class);
        $token = $this->prophesize(TokenInterface::class);

        $token->getUser()->shouldBeCalled()->willReturn($user->reveal());
        $attribute = 555;

        $voter = new PermissionVoter();
        $this->assertEquals(PermissionVoter::ACCESS_ABSTAIN, $voter->vote($token->reveal(), null, [$attribute]));
    }

    public function testItReturnsAccessDeniedIfCurrentUserDoesNotHavePermissions()
    {
        $user = $this->prophesize(User::class);
        $token = $this->prophesize(TokenInterface::class);

        $user->getPermissions()->shouldBeCalled()->willReturn(Permission::get(Permission::NONE));
        $token->getUser()->shouldBeCalled()->willReturn($user->reveal());
        $attribute = Permission::USER_VIEW_SELF;

        $voter = new PermissionVoter();
        $this->assertEquals(PermissionVoter::ACCESS_DENIED, $voter->vote($token->reveal(), null, [$attribute]));
    }

    public function testItReturnsAccessDeniedIfUserNotLoggedIn()
    {
        $token = $this->prophesize(TokenInterface::class);

        $token->getUser()->shouldBeCalled()->willReturn('anon.');
        $attribute = Permission::USER_VIEW_SELF;

        $voter = new PermissionVoter();
        $this->assertEquals(PermissionVoter::ACCESS_DENIED, $voter->vote($token->reveal(), null, [$attribute]));
    }
}
