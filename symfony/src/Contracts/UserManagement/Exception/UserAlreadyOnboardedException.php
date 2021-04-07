<?php

namespace App\Contracts\UserManagement\Exception;

use Ramsey\Uuid\UuidInterface;

class UserAlreadyOnboardedException extends \Exception
{
    public function __construct(UuidInterface $id)
    {
        parent::__construct(sprintf('User with id "%s" already onboarded', $id->toString()), 4203);
    }
}