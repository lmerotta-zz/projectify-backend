<?php

namespace App\Modules\UserManagement\FileStorage;

use App\Entity\Files\File;
use App\Contracts\FileManagement\Enum\FileContext;
use App\Contracts\FileManagement\Exception\FileNotFoundException;
use App\Contracts\FileManagement\FileRetrieverInterface;
use League\Flysystem\FilesystemInterface;
use League\Glide\Filesystem\FileNotFoundException as GlideFileNotFoundException;
use League\Glide\Responses\SymfonyResponseFactory;
use League\Glide\ServerFactory;
use League\Glide\Signatures\SignatureException;
use League\Glide\Signatures\SignatureFactory;

/**
 * Class UserProfilePictureRetriever
 * @package App\Modules\UserManagement\FileStorage
 * Is used to retrieve a user profile picture as a Symfony response
 */
class UserProfilePictureRetriever implements FileRetrieverInterface
{
    public function getSupportedContext(): FileContext
    {
        return FileContext::get(FileContext::USER_PROFILE_PICTURE);
    }

    public function retrieveFromEntity(File $entity, FilesystemInterface $source, FilesystemInterface $cache, array $options = [])
    {
        $context = $entity->getContext()->getValue();
        $name = $entity->getPath();
        $glideSecret = getenv('GLIDE_SECRET');
        $glide = ServerFactory::create(['source' => $source, 'cache' => $cache]);
        $glide->setResponseFactory(new SymfonyResponseFactory());

        if (count($options) > 0) {
            try {
                SignatureFactory::create($glideSecret)->validateRequest($context.'/'.$name, $options);
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