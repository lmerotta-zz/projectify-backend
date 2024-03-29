<?php

namespace App\Modules\UserManagement\GraphQL\Type;

use ApiPlatform\Core\GraphQl\Type\TypeConverterInterface;
use App\Contracts\UserManagement\Enum\UserStatus;
use App\Modules\UserManagement\GraphQL\Type\Definition\UserStatusType;
use GraphQL\Type\Definition\Type as GraphQLType;
use Symfony\Component\PropertyInfo\Type;

class UserStatusTypeConverter implements TypeConverterInterface
{
    public function __construct(private TypeConverterInterface $defaultTypeConverter)
    {
    }

    public function convertType(
        Type $type,
        bool $input,
        ?string $queryName,
        ?string $mutationName,
        ?string $subscriptionName,
        string $resourceClass,
        string $rootResource,
        ?string $property,
        int $depth
    ): GraphQLType|null|string {
        if (
            Type::BUILTIN_TYPE_OBJECT === $type->getBuiltinType() &&
            is_a($type->getClassName(), UserStatus::class, true)
        ) {
            return UserStatusType::NAME;
        }

        return $this->defaultTypeConverter->convertType(
            $type,
            $input,
            $queryName,
            $mutationName,
            $subscriptionName,
            $resourceClass,
            $rootResource,
            $property,
            $depth
        );
    }

    /**
     * @codeCoverageIgnore
     */
    public function resolveType(string $type): ?GraphQLType
    {
        return $this->defaultTypeConverter->resolveType($type);
    }
}
