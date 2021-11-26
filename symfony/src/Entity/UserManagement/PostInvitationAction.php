<?php

namespace App\Entity\UserManagement;

use App\Contracts\UserManagement\Enum\PostInvitationActionType;
use App\Repository\UserManagement\PostInvitationActionRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass=PostInvitationActionRepository::class)
 */
class PostInvitationAction
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     */
    private $id;

    /**
     * @ORM\Column(type="post_invitation_action")
     */
    private $type;

    /**
     * @ORM\Column(type="array")
     */
    private $payload = [];

    /**
     * @ORM\ManyToOne(targetEntity=Invitation::class, inversedBy="actions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $invitation;

    public static function create(UuidInterface $id, PostInvitationActionType $type, array $payload = []): self
    {
        $self = new static();
        $self->id = $id;
        $self->type = $type;
        $self->payload = $payload;

        return $self;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function getInvitation(): ?Invitation
    {
        return $this->invitation;
    }

    public function assignTo(Invitation $invitation): void
    {
        $this->invitation = $invitation;
    }

    public function removeFrom(Invitation $invitation): void
    {
        if ($this->invitation === $invitation) {
            $this->invitation = null;
        }
    }
}
