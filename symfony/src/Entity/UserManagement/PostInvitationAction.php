<?php

namespace App\Entity\UserManagement;

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
     * @ORM\Column(type="json")
     */
    private $action = [];

    /**
     * @ORM\ManyToOne(targetEntity=Invitation::class, inversedBy="actions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $invitation;

    public static function create(UuidInterface $id, array $action): self
    {
        $self = new static();
        $self->id = $id;
        $self->action = $action;

        return $self;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getAction(): array
    {
        return $this->action;
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
