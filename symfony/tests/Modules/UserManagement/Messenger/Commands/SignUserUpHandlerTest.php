<?php

namespace App\Tests\Modules\UserManagement\Messenger\Commands;

use App\Entity\Security\User;
use App\Modules\Common\Bus\EventBus;
use App\Modules\UserManagement\Messenger\Commands\SignUserUp;
use App\Modules\UserManagement\Messenger\Commands\SignUserUpHandler;
use App\Modules\UserManagement\Messenger\Events\UserSignedUp;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Prophecy\PhpUnit\ProphecyTrait;

class SignUserUpHandlerTest extends TestCase
{

    use ProphecyTrait;

    public function testItSavesTheUserInDatabase()
    {
        $em = $this->prophesize(EntityManagerInterface::class);
        $eventBus = $this->prophesize(EventBus::class);
        $logger = $this->prophesize(LoggerInterface::class);
        $encoderFactory = $this->prophesize(EncoderFactoryInterface::class);
        $encoder = $this->prophesize(PasswordEncoderInterface::class);

        $encoderFactory->getEncoder(User::class)->shouldBeCalled()->willReturn($encoder->reveal());
        $encoder->encodePassword('password', null)->shouldBeCalled()->willReturn('123abc');
        $em->persist(Argument::type(User::class))->shouldBeCalled();
        $em->flush()->shouldBeCalled();
        $eventBus->dispatch(Argument::type(UserSignedUp::class), [new DispatchAfterCurrentBusStamp()])->shouldBeCalled();

        $command = new SignUserUp('test@test.com', 'firtName', 'lastName', 'password');
        $handler = new SignUserUpHandler();
        $handler->setEntityManager($em->reveal());
        $handler->setEventBus($eventBus->reveal());
        $handler->setLogger($logger->reveal());
        $handler->setPasswordEncoder($encoderFactory->reveal());

        $user = $handler($command);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('test@test.com', $user->getEmail());
    }
}
