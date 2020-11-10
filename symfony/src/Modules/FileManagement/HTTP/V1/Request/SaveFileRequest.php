<?php

namespace App\Modules\FileManagement\HTTP\V1\Request;

use Elao\Enum\Bridge\Symfony\Validator\Constraint\Enum;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

class SaveFileRequest
{
    /**
     * @Enum(class="App\Modules\FileManagement\Enum\FileContext", asValue=true)
     */
    public string $context;

    /**
     * @OA\Property(property="file", type="file")
     * @Assert\NotBlank()
     * @Assert\File(groups={"user_profile_picture"}, maxSize="10M", mimeTypes={"image/jpeg", "image/jpg", "image/png"})
     */
    public ?UploadedFile $file;
}