<?php

namespace App\Modules\Common\Traits;

use Happyr\MessageSerializer\Serializer;
use Symfony\Contracts\Service\Attribute\Required;

trait MessageSerializer
{
    private Serializer $messageSerializer;

    #[Required]
    public function setMessageSerializer(Serializer $messageSerializer): void
    {
        $this->messageSerializer = $messageSerializer;
    }
}
