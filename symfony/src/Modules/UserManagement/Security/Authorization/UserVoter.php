<?php

namespace App\Modules\UserManagement\Security\Authorization;

use App\Contracts\Security\Enum\Permission;
use App\Entity\Security\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserVoter extends Voter
{
    protected function supports(string $attribute, $subject): bool
    {
        return match (intval($attribute)) {
            Permission::USER_VIEW_SELF, Permission::USER_EDIT_SELF => $subject instanceof User,
            default => false,
        };
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        return match (intval($attribute)) {
            Permission::USER_VIEW_SELF, Permission::USER_EDIT_SELF => $this->isSelf($token->getUser(), $subject),
            default => false,
        };
    }

    private function isSelf(User $current, User $subject): bool
    {
        return $current->getId()->equals($subject->getId());
    }
}
