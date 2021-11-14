<?php

namespace App\Modules\UserManagement\ApiPlatform;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\Entity\UserManagement\Team;
use App\Modules\Common\Traits\ImageCache;
use App\Modules\Common\Traits\Security;
use App\Modules\Common\Traits\UploaderHelper;
use App\Modules\UserManagement\Model\TeamDTO;

class TeamOutputDataTransformer implements DataTransformerInterface
{
    use Security;
    use UploaderHelper;
    use ImageCache;

    /**
     * @param Team $object
     */
    public function transform($object, string $to, array $context = []): TeamDTO
    {
        $output = new TeamDTO();
        $output->name = $object->getName();
        $output->createdAt = $object->getCreatedAt();
        $output->id = $object->getId();
        $output->owner = $object->getOwner();

        return $output;
    }

    /**
     * @codeCoverageIgnore
     */
    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return TeamDTO::class === $to && $data instanceof Team;
    }
}
