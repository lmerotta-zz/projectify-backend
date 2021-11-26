<?php

namespace App\Modules\UserManagement\Messenger\Commands;

use App\Entity\Security\User;
use App\Entity\UserManagement\Team;
use Symfony\Component\Validator\Constraints as Assert;

class AddMemberToTeam
{
    #[Assert\NotBlank]
    public Team $team;

    #[Assert\NotBlank]
    public User $user;

    public function __construct(Team $team, User $user)
    {
        $this->team = $team;
        $this->user = $user;
    }
}