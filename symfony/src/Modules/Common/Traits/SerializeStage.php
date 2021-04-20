<?php

namespace App\Modules\Common\Traits;

use ApiPlatform\Core\GraphQl\Resolver\Stage\SerializeStageInterface;
use Symfony\Contracts\Service\Attribute\Required;

trait SerializeStage
{
    private SerializeStageInterface $serializeStage;

    #[Required]
    public function setSerializeStage(SerializeStageInterface $serializeStage): void
    {
        $this->serializeStage = $serializeStage;
    }
}
