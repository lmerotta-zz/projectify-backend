<?php

namespace App\Modules\Common\Traits;

use App\Modules\Common\Bus\QueryBus as Bus;
use Symfony\Contracts\Service\Attribute\Required;

trait QueryBus
{
    private Bus $queryBus;

    #[Required]
    public function setQueryBus(Bus $queryBus): void
    {
        $this->queryBus = $queryBus;
    }
}
