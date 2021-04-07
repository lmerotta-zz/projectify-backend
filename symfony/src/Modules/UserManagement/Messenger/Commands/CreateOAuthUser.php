<?php

namespace App\Modules\UserManagement\Messenger\Commands;

use Symfony\Component\Validator\Constraints as Assert;

class CreateOAuthUser
{
    #[Assert\NotBlank]
    #[Assert\Email]
    public string $email;

    #[Assert\NotBlank]
    #[Assert\Choice(choices: ['githubId'])]
    public string $identifierField;

    #[Assert\NotBlank]
    public string $identifierValue;

    // The next one are optional and do not need an assert
    // as Oauth users may not have a first and last name

    public string $firstName;
    public string $lastName;

    public function __construct(
        string $email,
        string $firstName,
        string $lastName,
        string $identifierField,
        string $identifierValue
    ) {
        $this->email = $email;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->identifierField = $identifierField;
        $this->identifierValue = $identifierValue;
    }
}
