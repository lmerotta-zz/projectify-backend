<?php

namespace App\Modules\UserManagement\ApiPlatform\Extension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\ContextAwareQueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Contracts\Security\Enum\Permission;
use App\Entity\Security\User;
use App\Modules\Common\Traits\Security;
use Doctrine\ORM\QueryBuilder;

class UserExtension implements ContextAwareQueryCollectionExtensionInterface
{
    use Security;

    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        string $operationName = null,
        array $context = []
    ): void {
        $this->addKnownUsersDependency($queryBuilder, $resourceClass);
    }

    private function addKnownUsersDependency(QueryBuilder $queryBuilder, string $resourceClass): void
    {
        $rootAlias = $queryBuilder->getRootAliases()[0];

        if (User::class === $resourceClass) {
            if ($this->security->isGranted(Permission::USER_VIEW)) {
                $user = $this->security->getUser();
                $queryBuilder->leftJoin(sprintf('%s.teams', $rootAlias), 'user_teams');
                $queryBuilder->andWhere(
                    $queryBuilder->expr()->orX(
                        $queryBuilder->expr()->eq('user_teams.owner', ':current_user'),
                        $queryBuilder->expr()->isMemberOf(':current_user', 'user_teams.members')
                    )
                );
                $queryBuilder->setParameter('current_user', $user);
            }
        }
    }
}
