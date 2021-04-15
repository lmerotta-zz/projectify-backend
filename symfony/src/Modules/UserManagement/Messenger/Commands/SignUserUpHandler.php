<?php

namespace App\Modules\UserManagement\Messenger\Commands;

use App\Entity\Security\User;
use App\Modules\Common\Traits\EntityManager;
use App\Modules\Common\Traits\EventBus;
use App\Modules\Common\Traits\Logger;
use App\Modules\UserManagement\Messenger\Events\UserSignedUp;
use Psr\Log\LogLevel;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Contracts\Service\Attribute\Required;

class SignUserUpHandler
{
    use EntityManager;
    use EventBus;
    use Logger;

    private EncoderFactoryInterface $passwordEncoder;

    public function __invoke(SignUserUp $command): User
    {
        $user = User::create(
            Uuid::uuid4(),
            $command->firstName,
            $command->lastName,
            $this->passwordEncoder->getEncoder(User::class)->encodePassword($command->password, null),
            $command->email
        );

        $this->em->persist($user);
        $this->em->flush();

        $this->eventBus->dispatch(new UserSignedUp($user->getId()), [new DispatchAfterCurrentBusStamp()]);

        $this->logger->log(LogLevel::INFO, 'User signed up', ['id' => $user->getId(), 'email' => $user->getEmail()]);

        return $user;
    }

    #[Required]
    public function setPasswordEncoder(EncoderFactoryInterface $encoder): void
    {
        $this->passwordEncoder = $encoder;
    }
}
