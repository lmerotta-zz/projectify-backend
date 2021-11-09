<?php

namespace App\Modules\UserManagement\Model;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Contracts\Security\Enum\Permission;
use App\Contracts\UserManagement\Enum\UserStatus;
use Ramsey\Uuid\UuidInterface;

class UserDTO
{
    public UuidInterface $id;
    public string $firstName;
    public string $lastName;
    public string $email;
    public ?string $profilePictureUrl = null;
    public UserStatus $status;
    #[ApiProperty(description: 'Permission matrix. Available only for the currently logged in user')]
    public ?Permission $permissions = null;
}
