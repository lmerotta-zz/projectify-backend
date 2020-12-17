<?php

namespace App\Modules\FileManagement\Messenger\Queries;

use App\Contracts\FileManagement\Exception\FileNotFoundException;
use App\Modules\FileManagement\Exception\NonExistingFileRetrieverException;
use App\Modules\FileManagement\FileStorage\FileRetriever;
use App\Repository\Files\FileRepository;
use League\Flysystem\MountManager;

class GetFileResourceHandler
{
    private FileRepository $repository;
    private FileRetriever $fileRetriever;
    private MountManager $manager;

    public function __construct(FileRepository $repository, FileRetriever $fileRetriever, MountManager $manager)
    {
        $this->fileRetriever = $fileRetriever;
        $this->repository = $repository;
        $this->manager = $manager;
    }

    /**
     * @return mixed
     */
    public function __invoke(GetFileResource $query)
    {
        $file = $this->repository->find($query->getUuid());

        if (!$file) {
            throw new FileNotFoundException($query->getUuid()->toString());
        }

        $context = $file->getContext();
        $options = $query->getOptions();
        $source = $this->manager->getFilesystem($context->getValue() . '.storage');
        $cache = $this->manager->getFilesystem($context->getValue() . '.cache');

        try {
            return $this->fileRetriever->getRetriever($context)->retrieveFromEntity($file, $source, $cache, $options);
        } catch (NonExistingFileRetrieverException $e) {
            throw new FileNotFoundException('', 0, $e);
        }
    }
}
