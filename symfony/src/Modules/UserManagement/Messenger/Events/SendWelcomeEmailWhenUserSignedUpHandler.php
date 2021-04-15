<?php

namespace App\Modules\UserManagement\Messenger\Events;

use App\Repository\Security\UserRepository;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Contracts\Service\Attribute\Required;

class SendWelcomeEmailWhenUserSignedUpHandler
{
    private MailerInterface $mailer;
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

        $email = new Email();

        $email->addTo(new Address($user->getEmail(), $user->getFirstName().' '.$user->getLastName()))
        ->addFrom(new Address('l.merotta@gmail.com', 'Projectify'))
        ->text('testing text');
        $email->getHeaders()
            ->addTextHeader('templateId', 1)
            ->addParameterizedHeader('params', 'params', [
                'PRENOM' => $user->getFirstName(),
                'EMAIL' => $user->getEmail()
            ]);

        $this->mailer->send($email);

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
    public function setMailer(MailerInterface $mailer): void
    {
        $this->mailer = $mailer;
    }
}
