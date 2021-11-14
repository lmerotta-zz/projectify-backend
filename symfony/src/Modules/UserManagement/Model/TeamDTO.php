<?php

namespace App\Modules\UserManagement\Model;

use App\Entity\Security\User;
use Ramsey\Uuid\UuidInterface;

class TeamDTO
{
    public UuidInterface $id;
    public User $owner;
    public string $name;
    public \DateTimeImmutable $createdAt;
}