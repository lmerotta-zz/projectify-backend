<?php

namespace App\Contracts\UserManagement\Exception;

use App\Modules\Common\Exception\BaseException;
use Ramsey\Uuid\UuidInterface;

class UserNotFoundException extends BaseException
{
    public function __construct(UuidInterface $id)
    {
        parent::__construct(sprintf('User with id "%s" not found', $id->toString()), 404);
    }
}
