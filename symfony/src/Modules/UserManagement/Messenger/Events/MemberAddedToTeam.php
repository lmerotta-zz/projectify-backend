<?php

namespace App\Modules\UserManagement\Messenger\Events;

use Ramsey\Uuid\UuidInterface;

class MemberAddedToTeam
{
    public function __construct(private UuidInterface $teamId, private UuidInterface $memberId)
    {
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTeamId(): UuidInterface
    {
        return $this->teamId;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getMemberId(): UuidInterface
    {
        return $this->memberId;
    }
}
