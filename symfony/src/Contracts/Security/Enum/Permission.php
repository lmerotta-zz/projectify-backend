<?php

namespace App\Contracts\Security\Enum;

use Elao\Enum\AutoDiscoveredValuesTrait;
use Elao\Enum\FlaggedEnum;

class Permission extends FlaggedEnum
{
    use AutoDiscoveredValuesTrait;

    public const USER_VIEW_SELF = 1;
    public const USER_EDIT_SELF = 2;
    public const PROJECT_VIEW_OWN = 4;
    public const PROJECT_CREATE = 8;

    /**
     * @return string[]
     */
    public static function readables(): array
    {
        return [
            self::USER_VIEW_SELF => 'USER_VIEW_SELF',
            self::USER_EDIT_SELF => 'USER_EDIT_SELF',
            self::PROJECT_CREATE => 'PROJECT_CREATE',
            self::PROJECT_VIEW_OWN => 'PROJECT_VIEW_OWN',
        ];
    }
}
