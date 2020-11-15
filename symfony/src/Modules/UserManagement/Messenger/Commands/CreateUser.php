<?php

namespace App\Modules\UserManagement\Messenger\Commands;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class CreateUser
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