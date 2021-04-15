<?php

namespace App\Modules\Security\Authorization;

use App\Entity\Security\User;
use App\Modules\Common\Traits\CommandBus;
use App\Modules\UserManagement\Messenger\Commands\CreateOAuthUser;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\EntityUserProvider;

/**
 * @codeCoverageIgnore
 */
class OAuthUserProvider extends EntityUserProvider
{
    use CommandBus;

    public function loadUserByOAuthUserResponse(UserResponseInterface $response): User
    {
        $resourceOwnerName = $response->getResourceOwner()->getName();

        if (!isset($this->properties[$resourceOwnerName])) {
            $message = sprintf("No property defined for entity for resource owner '%s'.", $resourceOwnerName);
            throw new \RuntimeException($message);
        }

        $property = $this->properties[$resourceOwnerName];

        $username = $response->getUsername();
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
}
