<?php

namespace App\Modules\UserManagement\Messenger\Commands;

use App\Entity\Security\User;
use App\Modules\Common\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[UniqueEntity(
    className: User::class,
    fields: ['email' => 'email'],
    propertyPath: 'email'
)]
class SignUserUp
{
    #[Assert\NotBlank]
    #[Assert\Email]
    public string $email;

    #[Assert\NotBlank]
    public string $firstName;

    #[Assert\NotBlank]
    public string $lastName;

    #[Assert\NotBlank]
    public string $password;

    public function __construct(
        string $email,
        string $firstName,
        string $lastName,
        string $password
    ) {
        $this->email = $email;
        $this->password = $password;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }
}
