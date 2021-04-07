<?php

namespace App\Tests\Modules\UserManagement\Messenger\Commands;

use App\Entity\Security\User;
use App\Modules\Common\Bus\EventBus;
use App\Modules\UserManagement\Messenger\Commands\CreateOAuthUser;
use App\Modules\UserManagement\Messenger\Commands\CreateOAuthUserHandler;
use App\Modules\UserManagement\Messenger\Events\UserSignedUp;
use App\Repository\Security\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;

class CreateOAuthUserHandlerTest extends TestCase
{
    use ProphecyTrait;

    public function testItSetsTheOauthIdOnAFoundUser()
    {
        $user = $this->prophesize(User::class);
        $em = $this->prophesize(EntityManagerInterface::class);
        $logger = $this->prophesize(LoggerInterface::class);
        $repo = $this->prophesize(UserRepository::class);

        $email = 'test@test.com';
        $githubId = '12345';

        $user->getId()->shouldBeCalled()->willReturn(Uuid::uuid4());
        $user->getEmail()->shouldBeCalled()->willReturn($email);
        $user->setGithubId($githubId)->shouldBeCalled()->willReturn($user->reveal());
        $repo->findOneBy(['email' => $email])->shouldBeCalled()->willReturn($user->reveal());
        $em->flush()->shouldBeCalled();

        $handler = new CreateOAuthUserHandler();
        $handler->setLogger($logger->reveal());
        $handler->setUserRepository($repo->reveal());
        $handler->setEntityManager($em->reveal());

        $command = new CreateOAuthUser($email, '', '', 'githubId', $githubId);

        $handler($command);
    }

    public function testItCreatesAUserIfNotFound()
    {
        $eventBus = $this->prophesize(EventBus::class);
        $em = $this->prophesize(EntityManagerInterface::class);
        $logger = $this->prophesize(LoggerInterface::class);
        $repo = $this->prophesize(UserRepository::class);

        $email = 'test@test.com';
        $githubId = '12345';

        $repo->findOneBy(['email' => $email])->shouldBeCalled()->willReturn(null);
        $em->persist(Argument::type(User::class))->shouldBeCalled();
        $em->flush()->shouldBeCalled();
        $eventBus->dispatch(Argument::type(UserSignedUp::class), [new DispatchAfterCurrentBusStamp()])->shouldBeCalled();


        $handler = new CreateOAuthUserHandler();
        $handler->setLogger($logger->reveal());
        $handler->setUserRepository($repo->reveal());
        $handler->setEntityManager($em->reveal());
        $handler->setEventBus($eventBus->reveal());

        $command = new CreateOAuthUser($email,  'test', 'test', 'githubId', $githubId);

        $user = $handler($command);
        $this->assertEquals($githubId, $user->getGithubId());
    }
}
