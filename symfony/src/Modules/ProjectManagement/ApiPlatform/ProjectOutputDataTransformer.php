<?php

namespace App\Modules\ProjectManagement\ApiPlatform;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\Entity\ProjectManagement\Project;
use App\Modules\ProjectManagement\Model\ProjectDTO;

class ProjectOutputDataTransformer implements DataTransformerInterface
{
    /**
     * @param Project $object
     * @param string $to
     * @param array $context
     */
    public function transform($object, string $to, array $context = []): object|null
    {
        $output = new ProjectDTO();
        $output->id = $object->getId();
        $output->description = $object->getDescription();
        $output->name = $object->getName();
        $output->creator = $object->getCreator();
        $output->createdAt = $object->getCreatedAt();
        $output->updatedAt = $object->getUpdatedAt();

        return $output;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return ProjectDTO::class === $to && $data instanceof Project;
    }
}
