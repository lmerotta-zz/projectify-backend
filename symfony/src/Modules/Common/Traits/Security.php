<?php

namespace App\Modules\Common\Traits;

use Symfony\Component\Security\Core\Security as BaseSecurity;
use Symfony\Contracts\Service\Attribute\Required;

trait Security
{
    private BaseSecurity $security;

    #[Required]
    public function setSecurity(BaseSecurity $security): void
    {
        $this->security = $security;
    }
}
