<?php

namespace App\Modules\UserManagement\Messenger\Commands;

use Symfony\Component\Validator\Constraints as Assert;

class CreateTeam
{
    #[Assert\NotBlank]
    public string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }
}
