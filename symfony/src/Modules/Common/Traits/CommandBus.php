<?php

namespace App\Modules\Common\Traits;

use \App\Modules\Common\Bus\CommandBus as Bus;
use Symfony\Contracts\Service\Attribute\Required;

trait CommandBus
{
    private Bus $commandBus;

    #[Required]
    public function setCommandBus(Bus $commandBus): void
    {
        $this->commandBus = $commandBus;
    }
}