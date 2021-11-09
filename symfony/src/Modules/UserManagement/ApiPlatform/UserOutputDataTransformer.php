<?php

namespace App\Modules\UserManagement\ApiPlatform;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\Entity\Security\User;
use App\Modules\Common\Traits\ImageCache;
use App\Modules\Common\Traits\Security;
use App\Modules\Common\Traits\UploaderHelper;
use App\Modules\UserManagement\Model\UserDTO;

class UserOutputDataTransformer implements DataTransformerInterface
{
    use Security;
    use UploaderHelper;
    use ImageCache;

    /**
     * @param User $object
     */
    public function transform($object, string $to, array $context = []): UserDTO
    {
        $output = new UserDTO();
        $output->firstName = $object->getFirstName();
        $output->lastName = $object->getLastName();
        $output->id = $object->getId();
        $output->email = $object->getEmail();
        $output->status = $object->getStatus();

        $picturePath = $this->uploaderHelper->asset($object, 'profilePictureFile');
        if ($picturePath) {
            $output->profilePictureUrl = $this->imageCache->getBrowserPath($picturePath, 'user_profile_picture');
        }

        $connectedUser = $this->security->getUser();
        if ($connectedUser instanceof User && $connectedUser->getId()->equals($object->getId())) {
            $output->permissions = $object->getPermissions();
        }

        return $output;
    }

    /**
     * @codeCoverageIgnore
     */
    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return UserDTO::class === $to && $data instanceof User;
    }
}
