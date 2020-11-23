<?php
namespace App\Modules\Common\Model;

use JMS\Serializer\Annotation as Serializer;

class ValidationErrors
{
    /**
     * @Serializer\Type("string")
     */
    public ?string $propertyPath = null;

    /**
     * @Serializer\Type("string")
     */
    public ?string $message = null;
}
