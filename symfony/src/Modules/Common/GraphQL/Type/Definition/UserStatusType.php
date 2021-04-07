<?php


namespace App\Modules\Common\GraphQL\Type\Definition;


use ApiPlatform\Core\GraphQl\Type\Definition\TypeInterface;
use App\Contracts\UserManagement\Enum\UserStatus;
use GraphQL\Type\Definition\EnumType;

/**
 * @codeCoverageIgnore
 */
class UserStatusType extends EnumType implements TypeInterface
{
    public const NAME = 'UserStatus';

    public function __construct()
    {
        $all = array_reduce(
            UserStatus::instances(),
            function (array $carry, UserStatus $item) {
                $carry[$item->getValue()] = ['value' => $item];

                return $carry;
            },
            []
        );

        parent::__construct([
            'name' => self::NAME,
            'description' => 'Describes a user status',
            'values' => $all
        ]);
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function serialize($value)
    {
        if(is_string($value)) {
            return $value;
        }

        return parent::serialize($value);
    }
}