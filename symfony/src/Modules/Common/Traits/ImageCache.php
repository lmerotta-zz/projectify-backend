<?php

namespace App\Modules\Common\Traits;

use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Contracts\Service\Attribute\Required;

trait ImageCache
{
    private CacheManager $imageCache;

    #[Required]
    public function setImageCache(CacheManager $imageCache): void
    {
        $this->imageCache = $imageCache;
    }
}
