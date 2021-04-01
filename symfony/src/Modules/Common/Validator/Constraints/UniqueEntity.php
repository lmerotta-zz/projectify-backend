<?php

namespace App\Modules\Common\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @codeCoverageIgnore
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class UniqueEntity extends Constraint
{
    public string $message;
    public string $class;

    /**
     * @var array<string, string>
     */
    public array $fields;
    public string $propertyPath;

    public function __construct($options = null, array $groups = null, $payload = null, string $class, array $fields, string $propertyPath, ?string $message = null)
    {
        parent::__construct($options, $groups, $payload);

        $this->message = $message ?? 'common.errors.entity_not_unique';
        $this->class = $class;
        $this->fields = $fields;
        $this->propertyPath = $propertyPath;
    }

    /**
     * @return string[]
     */
    public function getTargets(): array
    {
        return [self::CLASS_CONSTRAINT];
    }
}
