<?php

namespace App\Modules\ProjectManagement\Security\Authorization;

use App\Contracts\Security\Enum\Permission;
use App\Entity\ProjectManagement\Project;
use App\Entity\Security\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ProjectVoter extends Voter
{
    protected function supports(string $attribute, $subject): bool
    {
        return match (intval($attribute)) {
            Permission::PROJECT_VIEW_OWN => !$subject || $subject instanceof Project,
            Permission::PROJECT_CREATE => true,
            default => false,
        };
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        return match (intval($attribute)) {
            Permission::PROJECT_VIEW_OWN => !$subject || $this->isOwnedBy($token->getUser(), $subject),
            Permission::PROJECT_CREATE => true,
            default => false,
        };
    }

    private function isOwnedBy(User $current, Project $subject): bool
    {
        return $current->getId()->equals($subject->getCreator()->getId());
    }
}
