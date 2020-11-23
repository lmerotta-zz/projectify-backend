<?php

namespace App\Modules\FileManagement\Stage;

use ApiPlatform\Core\EventListener\EventPriorities;
use ApiPlatform\Core\GraphQl\Resolver\Stage\ReadStageInterface;
use ApiPlatform\Core\GraphQl\Resolver\Stage\SerializeStageInterface;
use ApiPlatform\Core\Util\RequestAttributesExtractor;
use App\Entity\Files\File;
use App\Modules\FileManagement\FileStorage\FileRetriever;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\Response;

class GenerateFileURLSerializeStage implements SerializeStageInterface
{
    private SerializeStageInterface $stage;
    private FileRetriever $fileRetriever;

    public function __construct(FileRetriever $retriever, SerializeStageInterface $stage)
    {
        $this->fileRetriever = $retriever;
        $this->stage = $stage;
    }

    public function __invoke($itemOrCollection, string $resourceClass, string $operationName, array $context): ?array
    {
        $files = $itemOrCollection;
        $iterable = is_iterable($files);

        if (!$iterable) {
            $files = [$files];
        }

        if (\is_a($resourceClass, File::class, true)) {
            foreach ($files as $file) {
                if (!$file instanceof File) {
                    continue;
                }

                $file->setUrl($this->fileRetriever->getRetriever($file->getContext())->generateURL($file));
            }
        }

        return ($this->stage)(is_iterable($itemOrCollection) ? $files : $files[0], $resourceClass, $operationName, $context);
    }
}
