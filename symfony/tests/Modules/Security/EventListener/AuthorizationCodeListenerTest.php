<?php

namespace App\Tests\Modules\Security\EventListener;

use App\Entity\Security\User;
use App\Modules\Security\EventListener\AuthorizationCodeListener;
use League\OAuth2\Server\RequestTypes\AuthorizationRequest;
use PHPUnit\Framework\TestCase;
use Trikoder\Bundle\OAuth2Bundle\Event\AuthorizationRequestResolveEvent;
use Trikoder\Bundle\OAuth2Bundle\Model\Client;
use Prophecy\PhpUnit\ProphecyTrait;


class AuthorizationCodeListenerTest extends TestCase
{
    use ProphecyTrait;

    public function testItGrantsAccessIfValidUserInEvent()
    {
        $user = $this->prophesize(User::class);
        $event = new AuthorizationRequestResolveEvent(new AuthorizationRequest(), [], new Client('test', null));
        $event->setUser($user->reveal());

        $listener = new AuthorizationCodeListener();
        $listener->onAuthorizationRequestResolve($event);

        $this->assertEquals(AuthorizationRequestResolveEvent::AUTHORIZATION_APPROVED, $event->getAuthorizationResolution());
    }
}
