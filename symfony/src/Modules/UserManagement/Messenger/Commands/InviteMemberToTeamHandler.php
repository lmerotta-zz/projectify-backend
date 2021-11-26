<?php

namespace App\Modules\UserManagement\Messenger\Commands;

use App\Contracts\UserManagement\Enum\PostInvitationActionType;
use App\Entity\UserManagement\Invitation;
use App\Entity\UserManagement\PostInvitationAction;
use App\Modules\Common\Traits\CommandBus;
use App\Modules\Common\Traits\EntityManager;
use Doctrine\Common\Collections\Criteria;
use Ramsey\Uuid\Uuid;

class InviteMemberToTeamHandler
{
    use CommandBus;
    use EntityManager;

    public function __invoke(InviteMemberToTeam $command): void
    {
        /**
         * @var Invitation $invitation
         */
        $invitation = $this->commandBus->dispatch(new InviteUser($command->email));

        $action = PostInvitationAction::create(
            Uuid::uuid4(),
            PostInvitationActionType::get(PostInvitationActionType::ADD_TO_TEAM),
            ['team' => $command->team->getId()]
        );

        $criteria = Criteria::create();
        $criteria->andWhere(Criteria::expr()->eq('type', $action->getType()));

        $possibleInvitations = $invitation
            ->getActions()
            ->matching($criteria)
            ->filter(static function (PostInvitationAction $possible) use ($command) {
                return $possible->getPayload()['team'] === $command->team->getId()->toString();
            });

        if ($possibleInvitations->count() === 0) {
            $invitation->addAction($action);
        }

        $this->em->flush();
    }
}
