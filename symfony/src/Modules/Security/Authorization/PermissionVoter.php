<?php

namespace App\Modules\Security\Authorization;

use App\Contracts\Security\Enum\Permission;
use App\Entity\Security\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class PermissionVoter implements VoterInterface
{
    public function vote(TokenInterface $token, $subject, array $attributes): int
    {
        // abstain vote by default in case none of the attributes are supported
        $vote = self::ACCESS_ABSTAIN;

        $user = $token->getUser();

        foreach (array_filter($attributes, static fn ($item) => intval($item) !== 0) as $attribute) {
            $converted = intval($attribute);

            if (!Permission::accepts($converted)) {
                continue;
            }

            // abstain if not logged in
            if (!$user instanceof User) {
                return self::ACCESS_DENIED;
            }

            if (!$user->getPermissions()->hasFlag($converted)) {
                $vote = self::ACCESS_DENIED;
            }
        }

        return $vote;
    }
}
