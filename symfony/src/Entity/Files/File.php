<?php

namespace App\Entity\Files;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Contracts\FileManagement\Enum\FileContext;
use App\Repository\Files\FileRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use App\Modules\FileManagement\Messenger\Queries;

/**
 * @ORM\Entity(repositoryClass=FileRepository::class)
 * @ApiResource(
 *     collectionOperations={},
 *     itemOperations={
 *          "get"
 *     },
 *     graphql={
 *          "create"={
 *              "mutation"=App\Modules\FileManagement\GraphQL\Mutations\SaveFileResolver::class,
 *              "deserialize"=false,
 *              "args"={
 *                  "file"={"type"="Upload!", "description"="File to upload"},
 *                  "context"={"type"="String!", "description="="the upload context"}
 *              }
 *          },
 *          "item_query",
 *          "collection_query"
 *     }
 * )
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

    private string $url;

    private function __construct()
    {
    }

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

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}
