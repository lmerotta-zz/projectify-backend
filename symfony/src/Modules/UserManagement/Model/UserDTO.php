<?php

namespace App\Modules\UserManagement\Model;

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
}
