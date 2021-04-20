<?php

namespace App\Modules\UserManagement\Messenger\Queries;

use App\Entity\Security\User;
use App\Modules\Common\Traits\TokenStorage;

class GetCurrentUserHandler
{
    use TokenStorage;

    public function __invoke(GetCurrentUser $query): ?User
    {
        $tokenUser = $this->tokenStorage->getToken()->getUser();

        if ($tokenUser instanceof User) {
            return $tokenUser;
        }

        return null;
    }
}
