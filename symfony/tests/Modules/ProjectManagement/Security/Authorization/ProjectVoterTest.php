<?php

namespace App\Tests\Modules\ProjectManagement\Security\Authorization;

use App\Contracts\Security\Enum\Permission;
use App\Entity\ProjectManagement\Project;
use App\Entity\Security\User;
use App\Modules\ProjectManagement\Security\Authorization\ProjectVoter;
use App\Modules\Security\Authorization\PermissionVoter;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class ProjectVoterTest extends TestCase
{
    use ProphecyTrait;

    public function testItGrantsAccessIfProjectCreateIsGranted()
    {
        $subject = $this->prophesize(Project::class);
        $token = $this->prophesize(TokenInterface::class);

        $voter = new ProjectVoter();
        $this->assertEquals(PermissionVoter::ACCESS_GRANTED, $voter->vote($token->reveal(), $subject->reveal(), [Permission::PROJECT_CREATE]));
    }

    public function testItGrantsAccessIfProjectOwnAndSubject()
    {
        $id = Uuid::uuid4();
        $user = $this->prophesize(User::class);
        $subject = $this->prophesize(Project::class);
        $token = $this->prophesize(TokenInterface::class);

        $user->getId()->shouldBeCalled()->willReturn($id);
        $subject->getCreator()->shouldBeCalled()->willReturn($user->reveal());
        $token->getUser()->shouldBeCalled()->willReturn($user->reveal());

        $voter = new ProjectVoter();
        $this->assertEquals(PermissionVoter::ACCESS_GRANTED, $voter->vote($token->reveal(), $subject->reveal(), [Permission::PROJECT_VIEW]));
    }


    public function testIdDeniseAccessIfCreatorIsDifferent()
    {
        $id = Uuid::uuid4();
        $user = $this->prophesize(User::class);
        $creator = $this->prophesize(User::class);
        $subject = $this->prophesize(Project::class);
        $token = $this->prophesize(TokenInterface::class);

        $creator->getId()->shouldBeCalled()->willReturn(Uuid::uuid4());
        $user->getId()->shouldBeCalled()->willReturn($id);
        $subject->getCreator()->shouldBeCalled()->willReturn($creator->reveal());
        $token->getUser()->shouldBeCalled()->willReturn($user->reveal());

        $voter = new ProjectVoter();
        $this->assertEquals(PermissionVoter::ACCESS_DENIED, $voter->vote($token->reveal(), $subject->reveal(), [Permission::PROJECT_VIEW]));
    }

    public function testItGrantsAccessIfProjectOwnAndNoSubject()
    {
        $token = $this->prophesize(TokenInterface::class);

        $voter = new ProjectVoter();
        $this->assertEquals(PermissionVoter::ACCESS_GRANTED, $voter->vote($token->reveal(), null, [Permission::PROJECT_VIEW]));
    }
}
