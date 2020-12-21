<?php

namespace App\Modules\UserManagement\Messenger\Events;

use Ramsey\Uuid\UuidInterface;

class UserSignedUp
{
    private UuidInterface $id;

    public function __construct(UuidInterface $id)
    {
        $this->id = $id;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }
}
