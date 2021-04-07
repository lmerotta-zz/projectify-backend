<?php

namespace App\Modules\UserManagement\GraphQL\Resolver;

use ApiPlatform\Core\GraphQl\Resolver\QueryItemResolverInterface;
use App\Entity\Security\User;
use App\Modules\Common\Bus\QueryBus;
use App\Modules\UserManagement\Messenger\Queries\GetCurrentUser;
use Symfony\Contracts\Service\Attribute\Required;

/**
 * @codeCoverageIgnore
 */
class GetCurrentUserResolver implements QueryItemResolverInterface
{
    private QueryBus $queryBus;

    public function __invoke($item, array $context): ?User
    {
        return $this->queryBus->query(new GetCurrentUser());
    }

    #[Required]
    public function setQueryBus(QueryBus $queryBus): void
    {
        $this->queryBus = $queryBus;
    }
}
