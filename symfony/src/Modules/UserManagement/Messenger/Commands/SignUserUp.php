<?php

namespace App\Modules\UserManagement\Messenger\Commands;

use App\Entity\Security\User;
use App\Modules\Common\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @UniqueEntity(fields={"email": "email"}, class=User::class, propertyPath="email")
 */
class SignUserUp
{
    /**
     * @Assert\NotBlank
     */
    public string $email;

    /**
     * @Assert\NotBlank
     */
    public string $firstName;

    /**
     * @Assert\NotBlank
     */
    public string $lastName;

    /**
     * @Assert\NotBlank
     */
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
