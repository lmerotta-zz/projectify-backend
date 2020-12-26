<?php

namespace App\Tests\Modules\Security\Authorization;

use App\Contracts\Security\Enum\Permission;
use App\Entity\Security\Role;
use App\Entity\Security\User;
use App\Modules\Security\Authorization\PermissionVoter;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class PermissionVoterTest extends TestCase
{
    public function testItReturnsAbstainIfNotAUser()
    {
        $token = $this->prophesize(TokenInterface::class);
        $token->getUser()->shouldBeCalled()->willReturn('anon.');

        $voter = new PermissionVoter();
        $this->assertEquals(PermissionVoter::ACCESS_ABSTAIN, $voter->vote($token->reveal(), null, ['test']));
    }

    public function testItAbstainsIfAttributeIsNotAPermission()
    {
        $role = $this->prophesize(Role::class);
        $user = $this->prophesize(User::class);
        $token = $this->prophesize(TokenInterface::class);

        $role->getPermissions()->shouldBeCalled()->willReturn(Permission::get(Permission::USER_VIEW_SELF));
        $user->getInternalRoles()->shouldBeCalled()->willReturn(new ArrayCollection([$role->reveal()]));
        $token->getUser()->shouldBeCalled()->willReturn($user->reveal());
        $attribute = 555;

        $voter = new PermissionVoter();
        $this->assertEquals(PermissionVoter::ACCESS_ABSTAIN, $voter->vote($token->reveal(), null, [$attribute]));
    }

    public function testItReturnsAccessDeniedIfCurrentUserDoesNotHavePermissions()
    {
        $role = $this->prophesize(Role::class);
        $user = $this->prophesize(User::class);
        $token = $this->prophesize(TokenInterface::class);

        $role->getPermissions()->shouldBeCalled()->willReturn(Permission::get(Permission::NONE));
        $user->getInternalRoles()->shouldBeCalled()->willReturn(new ArrayCollection([$role->reveal()]));
        $token->getUser()->shouldBeCalled()->willReturn($user->reveal());
        $attribute = Permission::USER_VIEW_SELF;

        $voter = new PermissionVoter();
        $this->assertEquals(PermissionVoter::ACCESS_DENIED, $voter->vote($token->reveal(), null, [$attribute]));
    }
}
