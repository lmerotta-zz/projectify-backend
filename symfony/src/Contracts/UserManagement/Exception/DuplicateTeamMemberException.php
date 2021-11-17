<?php

namespace App\Contracts\UserManagement\Exception;

use App\Modules\Common\Exception\BaseException;
use Ramsey\Uuid\UuidInterface;

class DuplicateTeamMemberException extends BaseException
{
    public function __construct(UuidInterface $userId, UuidInterface $teamId)
    {
        parent::__construct(
            sprintf('User "%s" already member of team "%s"', $userId->toString(), $teamId->toString()),
            500
        );
    }
}
