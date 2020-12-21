<?php

namespace App\Modules\FileManagement\Stage;

use ApiPlatform\Core\GraphQl\Resolver\Stage\SerializeStageInterface;
use App\Entity\Files\File;
use App\Modules\FileManagement\FileStorage\FileRetriever;

class GenerateFileURLSerializeStage implements SerializeStageInterface
{
    private SerializeStageInterface $stage;
    private FileRetriever $fileRetriever;

    /**
     * @return File[]|null
     */
    public function __invoke($itemOrCollection, string $resourceClass, string $operationName, array $context): ?array
    {
        $files = is_iterable($itemOrCollection) ? $itemOrCollection : [$itemOrCollection];

        if (\is_a($resourceClass, File::class, true)) {
            foreach ($files as $file) {
                // @codeCoverageIgnoreStart
                if (!$file instanceof File) {
                    continue;
                }
                // @codeCoverageIgnoreEnd

                $file->setUrl($this->fileRetriever->getRetriever($file->getContext())->generateURL($file));
            }
        }

        return ($this->stage)($itemOrCollection, $resourceClass, $operationName, $context);
    }

    /**
     * @required
     */
    public function setFileRetriever(FileRetriever $retriever): void
    {
        $this->fileRetriever = $retriever;
    }

    /**
     * @required
     */
    public function setStage(SerializeStageInterface $stage): void
    {
        $this->stage = $stage;
    }
}
