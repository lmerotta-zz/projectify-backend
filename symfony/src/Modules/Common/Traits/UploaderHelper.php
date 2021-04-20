<?php

namespace App\Modules\Common\Traits;

use Symfony\Contracts\Service\Attribute\Required;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper as Helper;

trait UploaderHelper
{
    private Helper $uploaderHelper;

    #[Required]
    public function setUploaderHelper(Helper $uploaderHelper): void
    {
        $this->uploaderHelper = $uploaderHelper;
    }
}
