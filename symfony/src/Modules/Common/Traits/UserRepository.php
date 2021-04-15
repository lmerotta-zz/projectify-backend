<?php

namespace App\Modules\Common\Traits;

use App\Repository\Security\UserRepository as Repository;
use Symfony\Contracts\Service\Attribute\Required;

trait UserRepository
{
    private Repository $userRepository;

    #[Required]
    public function setUserRepository(Repository $userRepository): void
    {
        $this->userRepository = $userRepository;
    }
}