<?php

namespace App\Modules\FileManagement\Enum;

use Elao\Enum\AutoDiscoveredValuesTrait;
use Elao\Enum\Enum;

class FileContext extends Enum
{
    use AutoDiscoveredValuesTrait;

    const USER_PROFILE_PICTURE = 'user_profile_picture';
}