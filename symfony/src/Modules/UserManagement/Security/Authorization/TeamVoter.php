<?php

namespace App\Modules\UserManagement\Security\Authorization;

use App\Contracts\Security\Enum\Permission;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TeamVoter extends Voter
{
    protected function supports(string $attribute, $subject): bool
    {
        return match (intval($attribute)) {
            Permission::TEAM_CREATE => true,
            default => false,
        };
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        return match (intval($attribute)) {
            Permission::TEAM_CREATE => true,
            default => false,
        };
    }
}
