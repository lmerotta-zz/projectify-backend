<?php

namespace App\Modules\UserManagement\GraphQL\Resolver;

use ApiPlatform\Core\GraphQl\Resolver\MutationResolverInterface;
use App\Entity\Security\User;
use App\Modules\Common\Bus\CommandBus;
use App\Modules\UserManagement\Messenger\Commands\OnboardUser;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\Service\Attribute\Required;

class OnboardUserResolver implements MutationResolverInterface
{
    private CommandBus $commandBus;
    private TokenStorageInterface $tokenStorage;

    public function __invoke($item, array $context): User
    {
        $args = $context['args']['input'];
        $user = $this->tokenStorage->getToken()->getUser();
        if (!$user instanceof User) {
            throw new AccessDeniedHttpException();
        }

        return $this->commandBus->dispatch(
            new OnboardUser(
                $user->getId(),
                $args['picture'],
                $args['firstName'],
                $args['lastName']
            )
        );
    }

    #[Required]
    public function setTokenStorage(TokenStorageInterface $tokenStorage): void
    {
        $this->tokenStorage = $tokenStorage;
    }

    #[Required]
    public function setCommandBus(CommandBus $commandBus): void
    {
        $this->commandBus = $commandBus;
    }
}
