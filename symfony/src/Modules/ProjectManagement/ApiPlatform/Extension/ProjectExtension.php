<?php

namespace App\Modules\ProjectManagement\ApiPlatform\Extension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Contracts\Security\Enum\Permission;
use App\Entity\ProjectManagement\Project;
use App\Modules\Common\Traits\Security;
use Doctrine\ORM\QueryBuilder;

class ProjectExtension implements QueryItemExtensionInterface, QueryCollectionExtensionInterface
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
        $this->filterByCreator($queryBuilder, $resourceClass);
    }

    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        string $operationName = null
    ): void {
        $this->filterByCreator($queryBuilder, $resourceClass);
    }

    private function filterByCreator(QueryBuilder $queryBuilder, string $resourceClass): void
    {
        $rootAlias = $queryBuilder->getRootAliases()[0];

        if (Project::class === $resourceClass) {
            if ($this->security->isGranted(Permission::PROJECT_VIEW_OWN)) {
                $queryBuilder->andWhere(sprintf('%s.creator = :current_user', $rootAlias));
                $queryBuilder->setParameter('current_user', $this->security->getUser());
            }
        }
    }
}
