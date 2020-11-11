<?php

namespace App\Modules\FileManagement\Messenger\Commands;

use App\Contracts\FileManagement\Enum\FileContext;
use Ramsey\Uuid\Rfc4122\UuidV4;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class SaveFile
{
    private FileContext $context;
    private UploadedFile $file;
    private UuidInterface $id;

    public function __construct(UuidInterface $id, FileContext $context, UploadedFile $file)
    {
        $this->id = $id;
        $this->context = $context;
        $this->file = $file;
    }

    /**
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
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