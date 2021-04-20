<?php

namespace App\Modules\ProjectManagement\Messenger\Events;

use Ramsey\Uuid\UuidInterface;

class ProjectCreated
{
    public function __construct(private UuidInterface $id) {}

    /**
     * @codeCoverageIgnore
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }
}