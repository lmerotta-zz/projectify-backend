<?php

namespace App\Modules\UserManagement\ApiPlatform\Extension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Contracts\Security\Enum\Permission;
use App\Entity\UserManagement\Team;
use App\Modules\Common\Traits\Security;
use Doctrine\ORM\QueryBuilder;

class TeamExtension implements QueryItemExtensionInterface, QueryCollectionExtensionInterface
{
    use Security;

    public function applyToItem(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        array $identifiers,
        string $operationName = null,
        array $context = []
    ): void {
        $this->addUserDependencyQuery($queryBuilder, $resourceClass);
    }

    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        string $operationName = null
    ): void {
        $this->addUserDependencyQuery($queryBuilder, $resourceClass);
    }

    private function addUserDependencyQuery(QueryBuilder $queryBuilder, string $resourceClass): void
    {
        $rootAlias = $queryBuilder->getRootAliases()[0];

        if (Team::class === $resourceClass) {
            if ($this->security->isGranted(Permission::TEAM_VIEW)) {
                $user = $this->security->getUser();
                $queryBuilder->andWhere(
                    $queryBuilder->expr()->orX(
                        $queryBuilder->expr()->eq(sprintf("%s.owner", $rootAlias), ':current_user'),
                        $queryBuilder->expr()->isMemberOf(':current_user', sprintf("%s.members", $rootAlias))
                    )
                );
                $queryBuilder->setParameter('current_user', $user);
            }
        }
    }
}
