<?php

namespace App\Contracts\UserManagement\Enum;

use Elao\Enum\AutoDiscoveredValuesTrait;
use Elao\Enum\Enum;

class PostInvitationActionType extends Enum
{
    use AutoDiscoveredValuesTrait;

    public const ADD_TO_TEAM = 'ADD_TO_TEAM';
}
