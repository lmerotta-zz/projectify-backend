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
            'security_post_denormalize' => 'is_granted(constant("\\\App\\\Contracts\\\Security\\\Enum\\\Permission::PROJECT_CREATE"), object)',
        ],
        'item_query' => [
            'security' => 'is_granted(constant("\\\App\\\Contracts\\\Security\\\Enum\\\Permission::PROJECT_VIEW_OWN"), object)',
        ],
        'collection_query' => [
            'security' => 'is_granted(constant("\\\App\\\Contracts\\\Security\\\Enum\\\Permission::PROJECT_VIEW_OWN"))',
        ],
    ],
    itemOperations: [
        'get' => [
            'security' => 'is_granted(constant("\\\App\\\Contracts\\\Security\\\Enum\\\Permission::PROJECT_VIEW_OWN"), object)',
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
     * @var \DateTime $created
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime $updated
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;


    private function __construct()
    {
    }

    public static function create(UuidInterface $uuid, string $name, ?string $description): self
    {
        $self = new static();
        $self->id = $uuid;
        $self->setName($name);
        $self->setDescription($description);

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

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCreator(): ?User
    {
        return $this->creator;
    }

    public function setCreator(?User $creator): self
    {
        $this->creator = $creator;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     * @return Project
     */
    public function setCreatedAt(\DateTime $createdAt): Project
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     * @return Project
     */
    public function setUpdatedAt(\DateTime $updatedAt): Project
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

}
