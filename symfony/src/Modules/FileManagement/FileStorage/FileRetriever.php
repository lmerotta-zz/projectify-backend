<?php

namespace App\Modules\FileManagement\FileStorage;

use App\Contracts\FileManagement\Enum\FileContext;
use App\Contracts\FileManagement\FileRetrieverInterface;
use App\Modules\FileManagement\Exception\DuplicateFileRetrieverException;
use App\Modules\FileManagement\Exception\NonExistingFileRetrieverException;

class FileRetriever
{
    private array $retrievers = [];

    public function addRetriever(FileRetrieverInterface $fileRetriever): void
    {
        $context = $fileRetriever->getSupportedContext()->getValue();
        if (!empty($this->retrievers[$context])) {
            throw new DuplicateFileRetrieverException($context);
        }

        $this->retrievers[$context] = $fileRetriever;
    }

    public function getRetriever(FileContext $context): FileRetrieverInterface
    {
        $contextName = $context->getValue();
        if (empty($this->retrievers[$contextName])) {
            throw new NonExistingFileRetrieverException($contextName);
        }

        return $this->retrievers[$contextName];
    }
}
