<?php

namespace App\Modules\Security\Authorization;

use App\Entity\Security\User;
use App\Modules\Common\Traits\CommandBus;
use App\Modules\UserManagement\Messenger\Commands\CreateOAuthUser;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthAwareUserProviderInterface;

/**
 * @codeCoverageIgnore
 */
class OAuthUserProvider implements OAuthAwareUserProviderInterface
{
    use CommandBus;

    protected ObjectManager $em;
    protected ObjectRepository $repository;
    protected string $class;
    protected array $properties = [
        'identifier' => 'id',
    ];

    public function __construct(
        ManagerRegistry $registry,
        string $class,
        array $properties,
        ?string $managerName = null
    ) {
        $this->em = $registry->getManager($managerName);
        $this->class = $class;
        $this->properties = array_merge($this->properties, $properties);
        $this->repository = $this->em->getRepository($this->class);
    }

    public function loadUserByOAuthUserResponse(UserResponseInterface $response): User
    {
        $resourceOwnerName = $response->getResourceOwner()->getName();

        if (!isset($this->properties[$resourceOwnerName])) {
            $message = sprintf("No property defined for entity for resource owner '%s'.", $resourceOwnerName);
            throw new \RuntimeException($message);
        }

        $property = $this->properties[$resourceOwnerName];

        $username = $response->getUserIdentifier();
        $user = $this->findUser([$property => $username]);
        if (!$user) {
            $firstname = $response->getFirstName();
            $lastName = $response->getLastName();
            if (!$firstname || !$lastName) {
                [$firstname, $lastName] = explode(' ', $response->getRealName(), 2);
            }
            // create a new user
            $command = new CreateOAuthUser(
                $response->getEmail(),
                $firstname ?? '',
                $lastName ?? '',
                $property,
                $username
            );

            $user = $this->commandBus->dispatch($command);
        }

        return $user;
    }

    /**
     * @return UserInterface
     */
    protected function findUser(array $criteria): ?User
    {
        return $this->repository->findOneBy($criteria);
    }
}
