<?php

namespace App\Contracts\UserManagement\Exception;

use Ramsey\Uuid\UuidInterface;
use Throwable;

class UserNotFoundException extends \Exception
{
    public function __construct(UuidInterface $id)
    {
        parent::__construct(sprintf('User with id "%s" not found', $id->toString()), 404);
    }
}