<?php

namespace App\Modules\UserManagement\HTTP\V1\Request;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class CreateUserRequest
{
    /**
     * @Assert\NotBlank()
     * @Serializer\Type("string")
     */
    public string $firstName;

    /**
     * @Assert\NotBlank()
     * @Serializer\Type("string")
     */
    public string $lastName;

    /**
     * @Assert\NotBlank()
     * @Assert\Email()
     * @Serializer\Type("string")
     */
    public string $email;

    /**
     * @Assert\NotBlank()
     * @Serializer\Type("string")
     */
    public string $password;

    /**
     * @Assert\NotBlank()
     * @Serializer\Type("string")
     */
    public string $repeatPassword;
}