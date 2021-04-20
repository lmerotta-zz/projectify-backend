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

    public function __construct(string $name)
    {
        $this->name = $name;
    }
}
