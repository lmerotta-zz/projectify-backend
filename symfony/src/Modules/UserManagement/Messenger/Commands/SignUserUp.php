<?php

namespace App\Modules\UserManagement\Messenger\Commands;

use App\Entity\Security\User;
use App\Modules\Common\Validator\Constraints\UniqueEntity;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @UniqueEntity(fields={"email": "email"}, class=User::class, propertyPath="email")
 */
class SignUserUp
{
    /**
     * @Assert\NotBlank
     * @Serializer\Type("string")
     */
    private string $email;

    /**
     * @Assert\NotBlank
     * @Serializer\Type("string")
     */
    private string $firstName;

    /**
     * @Assert\NotBlank
     * @Serializer\Type("string")
     */
    private string $lastName;

    /**
     * @Assert\NotBlank
     * @Serializer\Type("string")
     */
    private string $password;

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

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}
