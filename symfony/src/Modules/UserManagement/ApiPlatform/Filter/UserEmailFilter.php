<?php

namespace App\Modules\UserManagement\ApiPlatform\Filter;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\ContextAwareFilterInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\Security\User;
use App\Modules\Common\Traits\Security;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\PropertyInfo\Type;

/**
 *  DEFAULT:
        WHERE
            USERS ARE MEMBERS OF TEAM CREATED BY USER
            OR WHERE USERS ARE MEMBERS OF SAME TEAM AS USER
        if email is partial:
            ADD TO DEFAULT WHERE
                AND USER.EMAIL LIKE "%email%'
        if email is a complete email
            OR WHERE
                ALL_USERS.email LIKE email
 */
final class UserEmailFilter extends AbstractFilter implements ContextAwareFilterInterface
{
    use Security;

    /**
     * {@inheritdoc}
     */
    public function apply(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        string $operationName = null,
        array $context = []
    ): void {
        if (empty($context['filters']) || empty($context['filters']['email'])) {
            $this->filterProperty(
                '',
                null,
                $queryBuilder,
                $queryNameGenerator,
                $resourceClass,
                $operationName,
                $context
            );
        }

        foreach ($context['filters'] as $property => $value) {
            $this->filterProperty(
                $this->denormalizePropertyName($property),
                $value,
                $queryBuilder,
                $queryNameGenerator,
                $resourceClass,
                $operationName,
                $context
            );
        }
    }

    protected function filterProperty(
        string $property,
        $value,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $nameGenerator,
        string $resourceClass,
        string $operationName = null
    ): void {

        // @codeCoverageIgnoreStart
        if (
            $property &&
            (!$this->isPropertyEnabled($property, $resourceClass) ||
                !$this->isPropertyMapped($property, $resourceClass))
        ) {
            return;
        }
        // @codeCoverageIgnoreEnd

        $rootAlias = $queryBuilder->getRootAliases()[0];

        $user = $this->security->getUser();
        $currentUserParam = ':'.$nameGenerator->generateParameterName('current_user');
        $queryBuilder->setParameter($currentUserParam, $user);

        $teamAlias = $nameGenerator->generateJoinAlias('user_teams');
        $queryBuilder->leftJoin(sprintf('%s.teams', $rootAlias), $teamAlias);

        $defaultOrXConditions = [
            $queryBuilder->expr()->eq(sprintf('%s.owner', $teamAlias), $currentUserParam),
            $queryBuilder->expr()->isMemberOf($currentUserParam, sprintf('%s.members', $teamAlias)),
        ];
        $defaultAndXConditions = [];

        $fullEmailOrXConditions = [];

        if (!empty($value)) {
            $emails = is_array($value) ? $value : [$value];

            foreach ($emails as $index => $email) {
                $emailParameterName = ':'.$nameGenerator->generateParameterName("email_${index}");
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $fullEmailOrXConditions[] = $queryBuilder->expr()->eq(
                        sprintf('%s.email', $rootAlias),
                        $emailParameterName
                    );
                    $defaultAndXConditions[] = $queryBuilder->expr()->eq(
                        sprintf('%s.email', $rootAlias),
                        $emailParameterName
                    );
                    $queryBuilder->setParameter($emailParameterName, $email);
                } else {
                    $defaultAndXConditions[] = $queryBuilder->expr()->like(
                        sprintf('%s.email', $rootAlias),
                        $emailParameterName
                    );
                    $queryBuilder->setParameter($emailParameterName, "%${email}%");
                }
            }
        }

        $queryBuilder->andWhere(
            $queryBuilder->expr()->orX(
                $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->orX(...$defaultOrXConditions),
                    ...$defaultAndXConditions
                ),
                ...$fullEmailOrXConditions,
            )
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getDescription(string $resourceClass): array
    {

        // @codeCoverageIgnoreStart
        if ($resourceClass !== User::class) {
            throw new \Exception('This filter can only be used on the User class');
        }
        // @codeCoverageIgnoreEnd

        $description = [];
        $description['email'] = [
            'property' => 'email',
            'type' => Type::BUILTIN_TYPE_STRING,
            'required' => false,
            'is_collection' => false,
        ];
        $description['email[]'] = [
            'property' => 'email',
            'type' => Type::BUILTIN_TYPE_STRING,
            'required' => false,
            'is_collection' => true,
        ];

        return $description;
    }
}
