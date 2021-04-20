<?php

namespace App\Modules\Common\Traits;

use App\Modules\Common\SendInBlueMailer;
use Symfony\Contracts\Service\Attribute\Required;

trait Mailer
{
    private SendInBlueMailer $mailer;

    #[Required]
    public function setMailer(SendInBlueMailer $mailer): void
    {
        $this->mailer = $mailer;
    }
}
