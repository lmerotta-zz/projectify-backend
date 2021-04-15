<?php

namespace App\Modules\UserManagement\GraphQL\Resolver;

use ApiPlatform\Core\GraphQl\Resolver\MutationResolverInterface;
use App\Entity\Security\User;
use App\Modules\Common\Traits\CommandBus;
use App\Modules\Common\Traits\TokenStorage;
use App\Modules\UserManagement\Messenger\Commands\OnboardUser;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class OnboardUserResolver implements MutationResolverInterface
{
    use CommandBus;
    use TokenStorage;

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
}
