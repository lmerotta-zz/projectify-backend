<?php

namespace App\Modules\FileManagement\Messenger\Queries;

use App\Contracts\FileManagement\Enum\FileContext;
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

    /**
     * @return UuidInterface
     */
    public function getUuid(): UuidInterface
    {
        return $this->uuid;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }
}