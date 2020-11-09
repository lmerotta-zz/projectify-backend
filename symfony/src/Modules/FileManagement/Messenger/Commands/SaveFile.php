<?php

namespace App\Modules\FileManagement\Messenger\Commands;

use App\Modules\FileManagement\Enum\FileContext;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class SaveFile
{
    private FileContext $context;
    private UploadedFile $file;

    public function __construct(FileContext $context, UploadedFile $file)
    {
        $this->context = $context;
        $this->file = $file;
    }

    /**
     * @return FileContext
     */
    public function getContext(): FileContext
    {
        return $this->context;
    }

    /**
     * @return UploadedFile
     */
    public function getFile(): UploadedFile
    {
        return $this->file;
    }
}