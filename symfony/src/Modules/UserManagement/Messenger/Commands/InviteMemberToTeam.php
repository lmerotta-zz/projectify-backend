<?php

namespace App\Modules\UserManagement\Messenger\Commands;

use App\Entity\UserManagement\Team;
use Symfony\Component\Validator\Constraints as Assert;

class InviteMemberToTeam
{
    #[Assert\Email]
    #[Assert\NotBlank]
    public string $email;

    #[Assert\NotBlank]
    public Team $team;

    public function __construct(string $email, Team $team)
    {
        $this->email = $email;
        $this->team = $team;
    }
}
