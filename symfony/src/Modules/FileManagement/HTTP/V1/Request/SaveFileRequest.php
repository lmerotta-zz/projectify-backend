<?php

namespace App\Modules\FileManagement\HTTP\V1\Request;

use Elao\Enum\Bridge\Symfony\Validator\Constraint\Enum;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class SaveFileRequest
{
    /**
     * @Enum(class="App\Modules\FileManagement\Enum\FileContext", asValue=true)
     */
    public string $context;

    /**
     * @Assert\File(groups={"user_profile_picture"}, maxSize="10M", mimeTypes={"image/jpeg", "image/jpg", "image/png"})
     */
    public ?UploadedFile $file;
}