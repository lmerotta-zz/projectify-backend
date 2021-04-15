<?php

namespace App\Modules\UserManagement\Messenger\Events;

use App\Modules\Common\SendInBlueMailer;
use App\Repository\Security\UserRepository;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Contracts\Service\Attribute\Required;

class SendWelcomeEmailWhenUserSignedUpHandler
{
    private SendInBlueMailer $mailer;
    private UserRepository $userRepository;
    private LoggerInterface $logger;

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

    #[Required]
    public function setUserRepository(UserRepository $userRepository): void
    {
        $this->userRepository = $userRepository;
    }

    #[Required]
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    #[Required]
    public function setMailer(SendInBlueMailer $mailer): void
    {
        $this->mailer = $mailer;
    }
}
