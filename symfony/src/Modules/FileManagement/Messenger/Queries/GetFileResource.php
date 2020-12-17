<?php

namespace App\Modules\FileManagement\Messenger\Queries;

use Ramsey\Uuid\UuidInterface;

class GetFileResource
{
    private UuidInterface $uuid;
    private array $options;

    public function __construct(UuidInterface $uuid, array $options = [])
    {
        $this->uuid = $uuid;
        $this->options = $options;
    }

    public function getUuid(): UuidInterface
    {
        return $this->uuid;
    }

    /**
     * @return mixed[]
     */
    public function getOptions(): array
    {
        return $this->options;
    }
}
