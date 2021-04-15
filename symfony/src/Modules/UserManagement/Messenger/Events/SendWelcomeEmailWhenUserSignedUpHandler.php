<?php

namespace App\Modules\UserManagement\Messenger\Events;

use App\Modules\Common\Traits\Logger;
use App\Modules\Common\Traits\Mailer;
use App\Modules\Common\Traits\UserRepository;
use Psr\Log\LogLevel;

class SendWelcomeEmailWhenUserSignedUpHandler
{
    use UserRepository;
    use Logger;
    use Mailer;

    public function __invoke(UserSignedUp $event): void
    {
        $this->logger->log(
            LogLevel::INFO,
            'Sending welcome email to user',
            ['user' => $event->getId()->toString()]
        );

        $user = $this->userRepository->find($event->getId());
        $this->mailer->send(1, [
            'PRENOM' => $user->getFirstName(),
            'EMAIL' => $user->getEmail(),
        ], [$user->getEmail() => $user->getFirstName().' '.$user->getLastName()]);
    }
}
