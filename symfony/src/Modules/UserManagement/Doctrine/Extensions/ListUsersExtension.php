<?php

namespace App\Modules\UserManagement\Doctrine\Extensions;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Contracts\Security\Enum\Permission;
use App\Entity\Security\User;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Security;

class ListUsersExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    private Security $security;

    public function applyToItem(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        array $identifiers,
        ?string $operationName = null,
        array $context = []
    ): void {
        $this->addCurrentUserFilter($queryBuilder, $resourceClass, Permission::get(Permission::USER_VIEW_SELF));
    }

    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?string $operationName = null
    ): void {
        $this->addCurrentUserFilter($queryBuilder, $resourceClass, Permission::get(Permission::USER_VIEW_LIST));
    }

    public function addCurrentUserFilter(
        QueryBuilder $queryBuilder,
        string $resourceClass,
        Permission $permission
    ): void {
        /**
         * @var User $user
         */
        $user = $this->security->getUser();

        if ($user && $user->getPermissions()->hasFlag($permission->getValue()) && $resourceClass === User::class) {
            // TODO: better query once we have teams set up
            $queryBuilder->andWhere(
                sprintf('%s.id = :current_user', $queryBuilder->getRootAliases()[0])
            );
            $queryBuilder->setParameter('current_user', $user->getId());
        }
    }

    /**
     * @required
     */
    public function setSecurity(Security $security): void
    {
        $this->security = $security;
    }
}
