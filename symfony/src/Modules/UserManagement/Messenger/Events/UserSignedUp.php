<?php

namespace App\Modules\UserManagement\Messenger\Events;

use Ramsey\Uuid\UuidInterface;

class UserSignedUp
{
    public function __construct(private UuidInterface $id)
    {
    }

    /**
     * @codeCoverageIgnore
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }
}
