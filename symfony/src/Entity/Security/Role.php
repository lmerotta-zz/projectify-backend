<?php

namespace App\Entity\Security;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\Security\RoleRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RoleRepository::class)
 * @ApiResource(
 *     itemOperations={"get"},
 *     collectionOperations={},
 *     graphql={
 *         "collection_query"
 *     }
 * )
 */
class Role
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=255)
     */
    private $name;

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
}
