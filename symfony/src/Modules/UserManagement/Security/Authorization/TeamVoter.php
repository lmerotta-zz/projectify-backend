<?php

namespace App\Modules\UserManagement\Security\Authorization;

use App\Contracts\Security\Enum\Permission;
use App\Entity\Security\User;
use App\Entity\UserManagement\Team;
use Doctrine\Common\Collections\Criteria;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TeamVoter extends Voter
{
    protected function supports(string $attribute, $subject): bool
    {
        return match (intval($attribute)) {
            Permission::TEAM_CREATE => true,
            Permission::TEAM_VIEW => $subject instanceof Team || $subject === null,
            Permission::TEAM_EDIT => $subject instanceof Team,
            default => false,
        };
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        return match (intval($attribute)) {
            Permission::TEAM_CREATE => true,
            Permission::TEAM_VIEW => ($subject === null ||
                ($this->isOwnerOf($subject, $user) ||
                    $this->isMemberOf($subject, $user))),
            Permission::TEAM_EDIT => $this->isOwnerOf($subject, $user),
            default => false,
        };
    }

    private function isOwnerOf(Team $team, User $user): bool
    {
        return $team->getOwner()->getId()->equals($user->getId());
    }

    private function isMemberOf(Team $team, User $user): bool
    {
        return $team->getMembers()->contains($user);
    }
}
