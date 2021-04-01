<?php

namespace App\Entity\Security;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Contracts\Security\Enum\Permission;
use App\Repository\Security\RoleRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RoleRepository::class)
 */
#[ApiResource(
    collectionOperations: [],
    graphql: [
        'collection_query',
    ],
    itemOperations: ['get']
)]
class Role
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="permission", nullable=true)
     */
    private $permissions;

    private function __construct()
    {
    }

    public static function create(string $name): self
    {
        $self = new static();
        $self->setName($name);

        return $self;
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

    public function getPermissions(): Permission
    {
        return $this->permissions;
    }

    public function setPermissions(Permission $permissions): self
    {
        $this->permissions = $permissions;

        return $this;
    }
}
