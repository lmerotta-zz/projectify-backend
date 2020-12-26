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
        switch ($attribute) {
            case Permission::USER_VIEW_SELF:
                return $subject instanceof User;
            // @codeCoverageIgnoreStart
            default:
                return false;
            // @codeCoverageIgnoreEnd
        }
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        switch ($attribute) {
            case Permission::USER_VIEW_SELF:
                return $this->isSelf($token->getUser(), $subject);
            // @codeCoverageIgnoreStart
            default:
                return false;
            // @codeCoverageIgnoreEnd
        }
    }

    private function isSelf(User $current, User $subject): bool
    {
        return $current->getId()->equals($subject->getId());
    }
}
