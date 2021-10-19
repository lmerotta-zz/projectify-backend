<?php

namespace App\Modules\UserManagement\Messenger\Events;

use App\Modules\Common\Traits\Logger;
use App\Modules\Common\Traits\Mailer;
use App\Modules\Common\Traits\UserRepository;
use Psr\Log\LogLevel;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

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

        try {
            $this->mailer->send(1, [
                'PRENOM' => $user->getFirstName(),
                'EMAIL' => $user->getEmail(),
            ], [$user->getEmail() => $user->getFirstName().' '.$user->getLastName()]);
            $this->logger->log(
                LogLevel::INFO,
                'Sent welcome email to user successfully',
                ['user' => $event->getId()->toString()]
            );
            // @codeCoverageIgnoreStart
        } catch (HandlerFailedException $e) {
            if ($e->getPrevious() instanceof TransportExceptionInterface) {
                $this->logger->log(
                    LogLevel::ERROR,
                    'Error sending welcome email to user',
                    ['user' => $event->getId()->toString(), 'reason' => $e->getPrevious()->getMessage()]
                );
            } else {
                throw $e;
            }
        }
        // @codeCoverageIgnoreEnd
    }
}
