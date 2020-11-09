<?php

namespace App\Entity\Files;

use App\Modules\FileManagement\Enum\FileContext;
use App\Repository\Files\FileRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Rfc4122\UuidV4;

/**
 * @ORM\Entity(repositoryClass=FileRepository::class)
 */
class File
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $path;

    /**
     * @ORM\Column(type="file_context", length=255)
     */
    private $context;

    private function __construct() {}

    public function getId(): ?UuidV4
    {
        return $this->id;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getContext(): ?FileContext
    {
        return $this->context;
    }

    public function setContext(FileContext $context): self
    {
        $this->context = $context;

        return $this;
    }
}
