<?php

namespace App\Modules\ProjectManagement\Model;

use App\Entity\Security\User;
use Ramsey\Uuid\UuidInterface;

class ProjectDTO
{
    public UuidInterface $id;
    public string $name;
    public ?string $description = null;
    public User $creator;
    public \DateTimeImmutable $createdAt;
    public ?\DateTimeImmutable $updatedAt = null;
}
