<?php

namespace App\Contracts\Security\Enum;

use Elao\Enum\AutoDiscoveredValuesTrait;
use Elao\Enum\FlaggedEnum;

class Permission extends FlaggedEnum
{
    use AutoDiscoveredValuesTrait;

    public const USER_VIEW_SELF = 1;
}
