<?php

namespace App\Contracts\Security\Enum;

use Elao\Enum\AutoDiscoveredValuesTrait;
use Elao\Enum\FlaggedEnum;

class Permission extends FlaggedEnum
{
    use AutoDiscoveredValuesTrait;

    public const USER_VIEW = 1;
    public const USER_EDIT = 2;
    public const PROJECT_VIEW = 4;
    public const PROJECT_CREATE = 8;
    public const TEAM_CREATE = 16;

    /**
     * @return string[]
     */
    public static function readables(): array
    {
        return [
            self::USER_VIEW => 'USER_VIEW',
            self::USER_EDIT => 'USER_EDIT',
            self::PROJECT_CREATE => 'PROJECT_CREATE',
            self::PROJECT_VIEW => 'PROJECT_VIEW',
            self::TEAM_CREATE => 'TEAM_CREATE',
        ];
    }
}
