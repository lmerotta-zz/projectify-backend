<?php

namespace App\Modules\FileManagement\Messenger\Queries;

use App\Modules\FileManagement\Exception\FileNotFoundException;
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
    private MountManager $manager;
    private FileRepository $repository;

    public function __construct(MountManager $manager, FileRepository $repository)
    {
        $this->manager = $manager;
        $this->repository = $repository;
    }

    public function __invoke(GetFileResource $query): StreamedResponse
    {
        $file = $this->repository->find($query->getUuid());

        if (!$file) {
            throw new FileNotFoundException($query->getUuid()->toString());
        }

        $context = $file->getContext();
        $name = $file->getPath();
        $options = $query->getOptions();
        $glideSecret = getenv('GLIDE_SECRET');
        $glide = ServerFactory::create(['source' => $this->manager->getFilesystem($context->getValue().'.storage'), 'cache' => $this->manager->getFilesystem($context->getValue().'.cache')]);
        $glide->setResponseFactory(new SymfonyResponseFactory());

        if (count($options) > 0) {
            try {
                SignatureFactory::create($glideSecret)->validateRequest($context->getValue().'/'.$name, $options);
            } catch (SignatureException $e) {
                throw new FileNotFoundException($e->getMessage());
            }
        }

        try {
            return $glide->getImageResponse($name, $options);
        } catch (GlideFileNotFoundException $e) {
            throw new FileNotFoundException($e->getMessage());
        }
    }
}