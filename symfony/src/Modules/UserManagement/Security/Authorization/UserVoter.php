<?php

namespace App\Modules\UserManagement\Security\Authorization;

use App\Contracts\Security\Enum\Permission;
use App\Entity\Security\User;
use App\Entity\UserManagement\Team;
use Doctrine\Common\Collections\Criteria;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserVoter extends Voter
{
    protected function supports(string $attribute, $subject): bool
    {
        return match (intval($attribute)) {
            Permission::USER_VIEW => $subject instanceof User || $subject === null,
            Permission::USER_EDIT => $subject instanceof User,
            default => false,
        };
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        return match (intval($attribute)) {
            Permission::USER_VIEW => $subject === null ||
                ($this->isSelf($token->getUser(), $subject) ||
                    $this->isMemberOfTeamOwnedBy($token->getUser(), $subject) ||
                    $this->isMemberOfSameTeamsAs($token->getUser(), $subject)),
            Permission::USER_EDIT => $this->isSelf($token->getUser(), $subject),
            default => false,
        };
    }

    private function isSelf(User $current, User $subject): bool
    {
        return $current->getId()->equals($subject->getId());
    }

    private function isMemberOfSameTeamsAs(User $current, User $subject): bool
    {
        $criteria = Criteria::create();
        $criteria->where(Criteria::expr()->memberOf('members', $current));
        return $subject->getTeams()->matching($criteria)->count() > 0;
    }

    private function isMemberOfTeamOwnedBy(User $current, User $subject): bool
    {
        $criteria = Criteria::create();
        $criteria->where(Criteria::expr()->eq('owner', $current));
        return $subject->getTeams()->matching($criteria)->count() > 0;
    }
}
