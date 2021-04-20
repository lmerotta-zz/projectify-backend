<?php

namespace App\Tests\Modules\UserManagement\Messenger\Queries;

use App\Entity\Security\User;
use App\Modules\UserManagement\Messenger\Queries\GetCurrentUser;
use App\Modules\UserManagement\Messenger\Queries\GetCurrentUserHandler;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class GetCurrentUserHandlerTest extends TestCase
{
    use ProphecyTrait;

    public function testItReturnsTheCurrentUser()
    {
        $user = $this->prophesize(User::class);
        $token = new AnonymousToken('', $user->reveal());
        $tokenStorage = $this->prophesize(TokenStorageInterface::class);

        $tokenStorage->getToken()->shouldBeCalled()->willReturn($token);

        $handler = new GetCurrentUserHandler();
        $handler->setTokenStorage($tokenStorage->reveal());

        $this->assertEquals($user->reveal(), $handler(new GetCurrentUser()));
    }

    public function testItReturnsNullIfNotAUser()
    {
        $token = new AnonymousToken('', 'test');
        $tokenStorage = $this->prophesize(TokenStorageInterface::class);

        $tokenStorage->getToken()->shouldBeCalled()->willReturn($token);

        $handler = new GetCurrentUserHandler();
        $handler->setTokenStorage($tokenStorage->reveal());

        $this->assertNull($handler(new GetCurrentUser()));
    }
}
