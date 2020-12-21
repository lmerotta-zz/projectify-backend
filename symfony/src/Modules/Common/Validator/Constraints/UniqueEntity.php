<?php

namespace App\Modules\Common\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @codeCoverageIgnore
 */
class UniqueEntity extends Constraint
{
    public string $message = 'common.errors.entity_not_unique';
    public string $class;

    /**
     * @var array<string, string>
     */
    public array $fields;
    public string $propertyPath;

    /**
     * @return string[]
     */
    public function getTargets(): array
    {
        return [self::CLASS_CONSTRAINT];
    }
}
