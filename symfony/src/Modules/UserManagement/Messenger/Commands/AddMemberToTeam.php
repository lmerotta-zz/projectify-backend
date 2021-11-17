<?php

namespace App\Modules\UserManagement\Messenger\Commands;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Entity\Security\User;
use App\Entity\UserManagement\Team;

class AddMemberToTeam
{
    public Team $team;
    public User $user;

    public function __construct(Team $team, User $user)
    {
        $this->team = $team;
        $this->user = $user;
    }
}
