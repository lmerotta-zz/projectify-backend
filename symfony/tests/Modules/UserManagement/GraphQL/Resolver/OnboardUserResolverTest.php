<?php

namespace App\Tests\Modules\UserManagement\GraphQL\Resolver;

use App\Entity\Security\User;
use App\Modules\Common\Bus\CommandBus;
use App\Modules\UserManagement\GraphQL\Resolver\OnboardUserResolver;
use App\Modules\UserManagement\Messenger\Commands\OnboardUser;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class OnboardUserResolverTest extends TestCase
{
    use ProphecyTrait;

    public function testItReturnsNullIfTokenStorageIsNotAUser()
    {
        $token = new AnonymousToken('123', '456');
        $tokenStorage = $this->prophesize(TokenStorageInterface::class);

        $tokenStorage->getToken()->shouldBeCalled()->willReturn($token);

        $resolver = new OnboardUserResolver();
        $resolver->setTokenStorage($tokenStorage->reveal());

        $result = $resolver(
            null,
            ['args' => ['input' => []]]
        );

        $this->assertNull($result);
    }

    public function testItDispatchesTheOnboardUserCommand()
    {
        $file = $this->getMockBuilder(File::class)->disableOriginalConstructor()->getMock();

        $id = Uuid::uuid4();
        $user = User::create($id, '', '', '', '');
        $token = new AnonymousToken('123', $user);
        $commandBus = $this->prophesize(CommandBus::class);
        $tokenStorage = $this->prophesize(TokenStorageInterface::class);

        $tokenStorage->getToken()->shouldBeCalled()->willReturn($token);
        $commandBus->dispatch(
            new OnboardUser(
                $id,
                $file,
                'first',
                'last'
            )
        )->shouldBeCalled()->willReturn($user);

        $resolver = new OnboardUserResolver();
        $resolver->setTokenStorage($tokenStorage->reveal());
        $resolver->setCommandBus($commandBus->reveal());

        $result = $resolver(
            null,
            ['args' => ['input' => [
                'picture' => $file,
                'firstName' => 'first',
                'lastName' => 'last'
            ]]]
        );

        $this->assertEquals($user, $result);
    }
}
