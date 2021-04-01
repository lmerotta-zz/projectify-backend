<?php

namespace App\Modules\UserManagement\Messenger\Commands;

use Symfony\Component\Validator\Constraints as Assert;

class CreateOAuthUser
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
     * @Assert\Choice({"githubId"})
     */
    public string $identifierField;

    /**
     * @Assert\NotBlank
     */
    public string $identifierValue;

    public function __construct(
        string $email,
        string $firstName,
        string $lastName,
        string $identifierField,
        string $identifierValue
    ) {
        $this->email = $email;
        $this->identifierField = $identifierField;
        $this->identifierValue = $identifierValue;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }
}
