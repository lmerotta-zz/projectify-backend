<?php

namespace App\Tests\Modules\UserManagement\Messenger\Commands;

use App\Contracts\UserManagement\Exception\UserAlreadyOnboardedException;
use App\Contracts\UserManagement\Exception\UserNotFoundException;
use App\Entity\Security\User;
use App\Modules\UserManagement\Messenger\Commands\OnboardUser;
use App\Modules\UserManagement\Messenger\Commands\OnboardUserHandler;
use App\Repository\Security\UserRepository;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Workflow\WorkflowInterface;

class OnboardUserHandlerTest extends TestCase
{
    use ProphecyTrait;

    public function testItThrowsUserNotFoundExceptionIfUserNotFound()
    {
        $this->expectException(UserNotFoundException::class);

        $repo = $this->prophesize(UserRepository::class);
        $repo->find(Argument::type(UuidInterface::class))->shouldBeCalled()->willReturn(null);

        $handler = new OnboardUserHandler();
        $handler->setUserRepository($repo->reveal());

        $handler(new OnboardUser(
            Uuid::uuid4(),
            $this->getMockBuilder(File::class)->disableOriginalConstructor()->getMock(),
            'test',
            'test'
        ));
    }

    public function testItThrowsExceptionIfUserAlreadyOnboarded()
    {
        $this->expectException(UserAlreadyOnboardedException::class);

        $user = $this->prophesize(User::class);
        $repo = $this->prophesize(UserRepository::class);
        $workflow = $this->prophesize(WorkflowInterface::class);

        $repo->find(Argument::type(UuidInterface::class))->shouldBeCalled()->willReturn($user->reveal());
        $workflow->can($user->reveal(), 'onboard')->shouldBeCalled()->willReturn(false);

        $handler = new OnboardUserHandler();
        $handler->setUserRepository($repo->reveal());
        $handler->setWorkflow($workflow->reveal());

        $handler(new OnboardUser(
            Uuid::uuid4(),
            $this->getMockBuilder(File::class)->disableOriginalConstructor()->getMock(),
            'test',
            'test'
        ));
    }

    public function testItWorksIfAllConditionsAreMet()
    {

        $user = $this->prophesize(User::class);
        $repo = $this->prophesize(UserRepository::class);
        $workflow = $this->prophesize(WorkflowInterface::class);

        $repo->find(Argument::type(UuidInterface::class))->shouldBeCalled()->willReturn($user->reveal());
        $workflow->can($user->reveal(), 'onboard')->shouldBeCalled()->willReturn(true);
        $workflow->apply($user->reveal(), 'onboard')->shouldBeCalled();

        $user->setFirstName('test')->shouldBeCalled()->willReturn($user->reveal());
        $user->setLastName('test')->shouldBeCalled()->willReturn($user->reveal());

        $handler = new OnboardUserHandler();
        $handler->setUserRepository($repo->reveal());
        $handler->setWorkflow($workflow->reveal());

        $result = $handler(new OnboardUser(
            Uuid::uuid4(),
            $this->getMockBuilder(File::class)->disableOriginalConstructor()->getMock(),
            'test',
            'test'
        ));

        $this->assertInstanceOf(User::class, $result);
    }
}
