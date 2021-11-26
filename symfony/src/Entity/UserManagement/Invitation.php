<?php

namespace App\Entity\UserManagement;

use App\Entity\Security\User;
use App\Repository\UserManagement\InvitationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=InvitationRepository::class)
 */
class Invitation
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $invitedBy;

    /**
     * @ORM\OneToMany(targetEntity=PostInvitationAction::class, mappedBy="invitation", orphanRemoval=true)
     */
    private $actions;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $expirationDate;

    /**
     * @ORM\OneToOne(targetEntity=User::class)
     */
    private $acceptedBy;

    private function __construct()
    {
        $this->actions = new ArrayCollection();
    }

    public static function create(UuidInterface $id, string $email, ?\DateTimeImmutable $customExpirationDate = null ): self
    {
        $self = new static();
        $self->id = $id;
        $self->email = $email;
        $self->expirationDate = $customExpirationDate ?? new \DateTimeImmutable("+10 days");

        return $self;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getInvitedBy(): ?User
    {
        return $this->invitedBy;
    }

    /**
     * @return Collection|PostInvitationAction[]
     */
    public function getActions(): Collection
    {
        return $this->actions;
    }

    public function addAction(PostInvitationAction $action): self
    {
        if (!$this->actions->contains($action)) {
            $this->actions[] = $action;
            $action->assignTo($this);
        }

        return $this;
    }

    public function removeAction(PostInvitationAction $action): self
    {
        if ($this->actions->removeElement($action)) {
            $action->removeFrom($this);
        }

        return $this;
    }

    public function getExpirationDate(): \DateTimeImmutable
    {
        return $this->expirationDate;
    }

    public function getAcceptedBy(): ?User
    {
        return $this->acceptedBy;
    }
}
