<?php

namespace App\Modules\Security\Authorization;

use App\Entity\Security\User;
use App\Modules\Common\Bus\CommandBus;
use App\Modules\UserManagement\Messenger\Commands\CreateOAuthUser;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\EntityUserProvider;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Contracts\Service\Attribute\Required;

class OAuthUserProvider extends EntityUserProvider
{
    private CommandBus $commandBus;

    #[Required]
    public function setCommandBus(CommandBus $commandBus): void
    {
        $this->commandBus = $commandBus;
    }

    public function loadUserByOAuthUserResponse(UserResponseInterface $response): User
    {
        $resourceOwnerName = $response->getResourceOwner()->getName();

        if (!isset($this->properties[$resourceOwnerName])) {
            throw new \RuntimeException(
                sprintf("No property defined for entity for resource owner '%s'.", $resourceOwnerName)
            );
        }

        $property = $this->properties[$resourceOwnerName];

        $username = $response->getUsername();
        $user = $this->findUser([$property => $username]);
        if (!$user) {
            $exception = new UsernameNotFoundException(sprintf("User '%s' not found.", $username));
            $exception->setUsername($username);

            // create a new user
            $command = new CreateOAuthUser(
                $response->getEmail(),
                $response->getFirstName() ?? $response->getNickname(),
                $response->getLastName() ?? $response->getNickName(),
                $property,
                $username
            );

            $user = $this->commandBus->dispatch($command);
        }

        return $user;
    }
}
