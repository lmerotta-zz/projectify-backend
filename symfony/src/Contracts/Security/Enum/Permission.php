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
}
