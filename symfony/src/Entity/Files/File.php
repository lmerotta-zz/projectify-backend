<?php

namespace App\Entity\Files;

use App\Modules\FileManagement\Enum\FileContext;
use App\Repository\Files\FileRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

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

    public static function create(UuidInterface $id, FileContext $context, string $path): self
    {
        $self = new static();
        $self->id = $id;
        $self->setContext($context)
            ->setPath($path);

        return $self;
    }

    public function getId(): ?UuidInterface
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
