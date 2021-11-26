<?php

namespace App\Contracts\UserManagement\Exception;

use App\Modules\Common\Exception\BaseException;
use Ramsey\Uuid\UuidInterface;

class UserAlreadyRegisteredException extends BaseException
{
    public function __construct(UuidInterface $userId)
    {
        parent::__construct(
            sprintf('User "%s" already completed his registration process', $userId->toString()),
            500
        );
    }
}
