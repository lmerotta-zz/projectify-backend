<?php

namespace App\Modules\UserManagement\GraphQL\Resolver;

use ApiPlatform\Core\GraphQl\Resolver\QueryItemResolverInterface;
use App\Entity\Security\User;
use App\Modules\Common\Traits\QueryBus;
use App\Modules\UserManagement\Messenger\Queries\GetCurrentUser;

/**
 * @codeCoverageIgnore
 */
class GetCurrentUserResolver implements QueryItemResolverInterface
{
    use QueryBus;

    public function __invoke($item, array $context): ?User
    {
        return $this->queryBus->query(new GetCurrentUser());
    }
}
