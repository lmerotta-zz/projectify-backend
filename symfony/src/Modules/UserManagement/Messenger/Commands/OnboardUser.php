<?php

namespace App\Modules\UserManagement\Messenger\Commands;

use Ramsey\Uuid\UuidInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

class OnboardUser
{
    #[Assert\NotNull]
    public UuidInterface $id;

    #[Assert\File(
        maxSize: '2M',
        mimeTypes: ['image/jpeg', 'image/png']
    )]
    #[Assert\NotNull]
    public File $profilePicture;

    #[Assert\NotBlank]
    public string $firstName;

    #[Assert\NotNull]
    public string $lastName;

    public function __construct(
        UuidInterface $id,
        File $profilePicture,
        string $firstName,
        string $lastName
    ) {
        $this->id = $id;
        $this->profilePicture = $profilePicture;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }
}
