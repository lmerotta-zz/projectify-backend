<?php


namespace App\Modules\Security\Authorization;


use App\Modules\Common\Bus\CommandBus;
use App\Modules\UserManagement\Messenger\Commands\CreateOAuthUser;
use App\Modules\UserManagement\Messenger\Commands\SignUserUp;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\EntityUserProvider;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

class OAuthUserProvider extends EntityUserProvider
{
    private CommandBus $commandBus;

    /**
     * @required
     */
    public function setCommandBus(CommandBus $commandBus): void
    {
        $this->commandBus = $commandBus;
    }

    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {

        $resourceOwnerName = $response->getResourceOwner()->getName();

        if (!isset($this->properties[$resourceOwnerName])) {
            throw new \RuntimeException(sprintf("No property defined for entity for resource owner '%s'.", $resourceOwnerName));
        }

        $property = $this->properties[$resourceOwnerName];

        $username = $response->getUsername();
        if (null === $user = $this->findUser([$property => $username])) {
            $exception = new UsernameNotFoundException(sprintf("User '%s' not found.", $username));
            $exception->setUsername($username);

            // create a new user
            $command = new CreateOAuthUser($response->getEmail(), $response->getFirstName() ?? $response->getNickname(), $response->getLastName() ?? $response->getNickName(), $property, $username);
            return $this->commandBus->dispatch($command);

        }

        return $user;
    }
}