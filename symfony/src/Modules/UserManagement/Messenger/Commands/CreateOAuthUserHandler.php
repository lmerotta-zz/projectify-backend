<?php

namespace App\Modules\UserManagement\Messenger\Commands;

use App\Entity\Security\User;
use App\Modules\Common\Traits\EntityManager;
use App\Modules\Common\Traits\EventBus;
use App\Modules\Common\Traits\Logger;
use App\Modules\Common\Traits\UserRepository;
use App\Modules\UserManagement\Messenger\Events\UserSignedUp;
use Psr\Log\LogLevel;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;
use Symfony\Component\PropertyAccess\PropertyAccess;

class CreateOAuthUserHandler
{
    use EntityManager;
    use EventBus;
    use UserRepository;
    use Logger;

    public function __invoke(CreateOAuthUser $command): User
    {
        $propertyAccess = PropertyAccess::createPropertyAccessor();

        // first find a user with the same email
        $user = $this->userRepository->findOneBy(['email' => $command->email]);

        // if not found, create it, otherwhise set its identifier ID
        if (!$user) {
            $user = User::createFromOAuth(Uuid::uuid4(), $command->firstName, $command->lastName, $command->email);
            $this->em->persist($user);
            $this->eventBus->dispatch(new UserSignedUp($user->getId()), [new DispatchAfterCurrentBusStamp()]);
        }

        $propertyAccess->setValue($user, $command->identifierField, $command->identifierValue);

        $this->em->flush();

        $this->logger->log(
            LogLevel::INFO,
            'User signed up using oauth',
            ['id' => $user->getId(), 'email' => $user->getEmail(), 'identifierValue' => $command->identifierValue]
        );

        return $user;
    }
}
