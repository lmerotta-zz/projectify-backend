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

    public static function create(string $name, Permission $permissions): self
    {
        $self = new static();
        $self->name = $name;
        $self->permissions = $permissions;

        return $self;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getPermissions(): Permission
    {
        return $this->permissions;
    }
}
