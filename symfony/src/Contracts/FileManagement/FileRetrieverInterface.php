<?php

namespace App\Contracts\FileManagement;

use App\Entity\Files\File;
use App\Contracts\FileManagement\Enum\FileContext;
use League\Flysystem\FilesystemInterface;

interface FileRetrieverInterface
{
    public function getSupportedContext(): FileContext;
    public function retrieveFromEntity(File $entity, FilesystemInterface $source, FilesystemInterface $cache, array $options = []);
    public function generateURL(File $entity): string;
}