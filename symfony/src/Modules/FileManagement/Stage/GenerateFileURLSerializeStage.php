<?php

namespace App\Modules\FileManagement\Stage;

use ApiPlatform\Core\GraphQl\Resolver\Stage\SerializeStageInterface;
use App\Entity\Files\File;
use App\Modules\FileManagement\FileStorage\FileRetriever;

class GenerateFileURLSerializeStage implements SerializeStageInterface
{
    private SerializeStageInterface $stage;
    private FileRetriever $fileRetriever;

    public function __construct(FileRetriever $retriever, SerializeStageInterface $stage)
    {
        $this->fileRetriever = $retriever;
        $this->stage = $stage;
    }

    /**
     * @return File[]|null
     */
    public function __invoke($itemOrCollection, string $resourceClass, string $operationName, array $context): ?array
    {
        $files = is_iterable($itemOrCollection) ? $itemOrCollection : [$itemOrCollection];

        if (\is_a($resourceClass, File::class, true)) {
            foreach ($files as $file) {
                if (!$file instanceof File) {
                    continue;
                }

                $file->setUrl($this->fileRetriever->getRetriever($file->getContext())->generateURL($file));
            }
        }

        return ($this->stage)($itemOrCollection, $resourceClass, $operationName, $context);
    }
}
