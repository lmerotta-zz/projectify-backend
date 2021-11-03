<?php

namespace App\Modules\ProjectManagement\Messenger\Commands;

use Symfony\Component\Validator\Constraints as Assert;

class CreateProject
{
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 1,
        max: 255
    )]
    public string $name;

    public ?string $description;

    public function __construct(string $name, ?string $description)
    {
        $this->name = $name;
        $this->description = $description;
    }
}
