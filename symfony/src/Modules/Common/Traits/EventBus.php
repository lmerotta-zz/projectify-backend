<?php

namespace App\Modules\Common\Traits;

use App\Modules\Common\Bus\EventBus as Bus;
use Symfony\Contracts\Service\Attribute\Required;

trait EventBus
{
    private Bus $eventBus;

    #[Required]
    public function setEventBus(Bus $eventBus): void
    {
        $this->eventBus = $eventBus;
    }
}
