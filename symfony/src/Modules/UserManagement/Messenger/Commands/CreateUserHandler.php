<?php

namespace App\Modules\UserManagement\Messenger\Commands;

use App\Entity\Security\User;
use Ramsey\Uuid\Uuid;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class CreateUserHandler
{
    public function __invoke(CreateUser $command)
    {
        $id = Uuid::uuid4();
        return User::create($id, $command->firstName, $command->lastName, $command->email, $command->password);
    }
}
