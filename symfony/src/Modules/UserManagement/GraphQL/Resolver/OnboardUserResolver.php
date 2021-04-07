<?php


namespace App\Modules\UserManagement\GraphQL\Resolver;


use ApiPlatform\Core\GraphQl\Resolver\MutationResolverInterface;
use App\Modules\Common\Bus\CommandBus;
use App\Modules\UserManagement\Messenger\Commands\OnboardUser;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\Service\Attribute\Required;

class OnboardUserResolver implements MutationResolverInterface
{
    private CommandBus $commandBus;
    private TokenStorageInterface $tokenStorage;

    /**
     * @inheritdoc
     */
    public function __invoke($item, array $context): mixed
    {
        $args = $context['args']['input'];
        return $this->commandBus->dispatch(
            new OnboardUser(
                $this->tokenStorage->getToken()->getUser()->getId(),
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