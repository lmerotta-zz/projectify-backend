<?php

namespace App\Entity\ProjectManagement;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Security\User;
use App\Modules\ProjectManagement\Messenger\Commands\CreateProject;
use App\Modules\ProjectManagement\Model\ProjectDTO;
use App\Repository\ProjectManagement\ProjectRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass=ProjectRepository::class)
 */
#[ApiResource(
    collectionOperations: [],
    graphql: [
        'create' => [
            'input' => CreateProject::class,
            'messenger' => 'input',
            'security_post_denormalize' => 'is_granted(permission("PROJECT_CREATE"))',
        ],
        'item_query' => [
            'security' => 'is_granted(permission("PROJECT_VIEW"), object)',
        ],
        'collection_query' => [
            'security' => 'is_granted(permission("PROJECT_VIEW"))',
        ],
    ],
    itemOperations: [
        'get' => [
            'security' => 'is_granted(permission("PROJECT_VIEW"), object)',
        ],
    ],
    order: ["createdAt" => "DESC"],
    output: ProjectDTO::class,
)]
class Project
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
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $creator;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @var \DateTimeImmutable $createdAt
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @var \DateTimeImmutable|null $updatedAt
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="date_immutable", nullable=true)
     */
    private $updatedAt;


    private function __construct()
    {
    }

    public static function create(UuidInterface $uuid, string $name, ?string $description = null): self
    {
        $self = new static();
        $self->id = $uuid;
        $self->name = $name;
        $self->description = $description;

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

    public function getCreator(): User
    {
        return $this->creator;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

}
