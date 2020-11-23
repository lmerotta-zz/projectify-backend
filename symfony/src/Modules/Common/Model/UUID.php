<?php

namespace App\Modules\Common\Model;

use JMS\Serializer\Annotation as Serializer;

class UUID
{
    /**
     * @Serializer\Type("string")
     */
    public string $uuid;
}
