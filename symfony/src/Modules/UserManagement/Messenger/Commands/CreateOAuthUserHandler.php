<?php

namespace App\Modules\UserManagement\Messenger\Commands;

use App\Entity\Security\User;
use App\Modules\Common\Bus\EventBus;
use App\Modules\UserManagement\Messenger\Events\UserSignedUp;
use App\Repository\Security\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Contracts\Service\Attribute\Required;

class CreateOAuthUserHandler
{
    private EntityManagerInterface $em;
    private EventBus $eventBus;
    private UserRepository $userRepository;
    private LoggerInterface $logger;

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

    #[Required]
    public function setEntityManager(EntityManagerInterface $em): void
    {
        $this->em = $em;
    }

    #[Required]
    public function setEventBus(EventBus $eventBus): void
    {
        $this->eventBus = $eventBus;
    }

    #[Required]
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    #[Required]
    public function setUserRepository(UserRepository $userRepository): void
    {
        $this->userRepository = $userRepository;
    }
}
