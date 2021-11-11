<?php

namespace App\Modules\UserManagement\GraphQL\Type\Definition;

use ApiPlatform\Core\GraphQl\Type\Definition\TypeInterface;
use App\Contracts\Security\Enum\Permission;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

/**
 * @codeCoverageIgnore
 */
class UserPermissionsType extends ObjectType implements TypeInterface
{
    public const NAME = 'UserPermissions';

    public function __construct()
    {
        parent::__construct([
            'name' => self::NAME,
            'description' => 'Describes a users\' permission matrix',
            'fields' => array_reduce(
                Permission::readables(),
                static function (array $carry, string $permission) {
                    $carry[$permission] = [
                        'type' => Type::nonNull(Type::boolean()),
                        'resolve' => static function (int $permissionValue) use ($permission) {
                            return Permission::get($permissionValue)->hasFlag(
                                constant(Permission::class.'::'.$permission)
                            );
                        },
                    ];

                    return $carry;
                },
                []
            ),
        ]);
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
