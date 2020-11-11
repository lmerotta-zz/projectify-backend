<?php

namespace App\Modules\FileManagement\Messenger\Queries;

use App\Contracts\FileManagement\Exception\FileNotFoundException;
use App\Modules\FileManagement\Exception\NonExistingFileRetrieverException;
use App\Modules\FileManagement\FileStorage\FileRetriever;
use League\Glide\Filesystem\FileNotFoundException as GlideFileNotFoundException;
use App\Repository\Files\FileRepository;
use League\Flysystem\MountManager;
use League\Glide\Responses\SymfonyResponseFactory;
use League\Glide\ServerFactory;
use League\Glide\Signatures\SignatureException;
use League\Glide\Signatures\SignatureFactory;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

    public function __invoke(GetFileResource $query)
    {
        $file = $this->repository->find($query->getUuid());

        if (!$file) {
            throw new FileNotFoundException($query->getUuid()->toString());
        }

        $context = $file->getContext();
        $options = $query->getOptions();
        $source = $this->manager->getFilesystem($context->getValue().'.storage');
        $cache = $this->manager->getFilesystem($context->getValue().'.cache');

        try {
            return $this->fileRetriever->getRetriever($context)->retrieveFromEntity($file, $source, $cache, $options);
        } catch (NonExistingFileRetrieverException $e) {
            throw new FileNotFoundException('', 0, $e);
        }
    }
}