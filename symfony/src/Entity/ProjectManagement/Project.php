<?php

namespace App\Entity\ProjectManagement;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Modules\ProjectManagement\Messenger\Commands\CreateProject;
use Gedmo\Mapping\Annotation as Gedmo;
use App\Entity\Security\User;
use App\Repository\ProjectManagement\ProjectRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass=ProjectRepository::class)
 */
#[ApiResource(
    collectionOperations: [],
    graphql: [
        'create' => [
            'input' => CreateProject::class,
            'messenger' => 'input'
        ],
    ],
    itemOperations: [
        'get'
    ]
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

    private function __construct() {}

    public static function create(UuidInterface $uuid, string $name): self
    {
        $self = new static();
        $self->id = $uuid;
        $self->setName($name);

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
}
