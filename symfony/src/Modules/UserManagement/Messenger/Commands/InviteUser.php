<?php

namespace App\Modules\UserManagement\Messenger\Commands;

use Symfony\Component\Validator\Constraints as Assert;

class InviteUser
{
    #[Assert\Email]
    #[Assert\NotBlank]
    public string $email;

    public function __construct(string $email)
    {
        $this->email = $email;
    }
}
