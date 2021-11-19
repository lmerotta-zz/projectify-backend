<?php

namespace App\Entity\UserManagement;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Security\User;
use App\Modules\UserManagement\Messenger\Commands\AddMemberToTeam;
use App\Modules\UserManagement\Messenger\Commands\CreateTeam;
use App\Modules\UserManagement\Model\TeamDTO;
use App\Repository\UserManagement\TeamRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass=TeamRepository::class)
 */
#[ApiResource(
    collectionOperations: [],
    graphql: [
        'create' => [
            'input' => CreateTeam::class,
            'messenger' => 'input',
            'security_post_denormalize' => 'is_granted(permission("TEAM_CREATE"))',
        ],
        'addMemberTo' => [
            'input' => AddMemberToTeam::class,
            'messenger' => 'input',
            'args' => [
                'team' => ['type' => 'ID!'],
                'user' => ['type' => 'ID!'],
            ],
            'security_post_denormalize' => 'is_granted(permission("TEAM_EDIT"), object.team)',
        ],
        'collection_query' => [
            'security' => 'is_granted(permission("TEAM_VIEW"))',
        ],
        'item_query' => [
            'security' => 'is_granted(permission("TEAM_VIEW"), object)',
        ],
    ],
    order: ["createdAt" => "DESC"],
    output: TeamDTO::class
)]
class Team
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="teams")
     */
    private $members;

    /**
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $owner;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $archivedAt;

    private function __construct()
    {
        $this->members = new ArrayCollection();
    }

    public static function create(UuidInterface $uuid, string $name): Team
    {
        $self = new static();
        $self->id = $uuid;
        $self->name = $name;

        return $self;
    }

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return Collection|User[]
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }

    public function addMember(User $member): self
    {
        if (!$this->members->contains($member)) {
            $this->members[] = $member;
        }

        return $this;
    }

    public function removeMember(User $member): self
    {
        $this->members->removeElement($member);

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getArchivedAt(): ?\DateTimeImmutable
    {
        return $this->archivedAt;
    }
}
