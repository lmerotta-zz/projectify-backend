<?php

namespace App\Contracts\FileManagement;

use App\Contracts\FileManagement\Enum\FileContext;
use App\Entity\Files\File;
use League\Flysystem\FilesystemInterface;

interface FileRetrieverInterface
{
    public function getSupportedContext(): FileContext;

    /**
     * @return mixed
     */
    public function retrieveFromEntity(
        File $entity,
        FilesystemInterface $source,
        FilesystemInterface $cache,
        array $options = []
    );

    public function generateURL(File $entity): string;
}
