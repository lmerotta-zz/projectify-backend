<?php

namespace App\Modules\Common;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Contracts\Service\Attribute\Required;

class SendInBlueMailer
{
    private MailerInterface $mailer;

    /**
     * @param array<string,string> $mergeTags
     * @param array<string,string> $recipients
     */
    public function send(int $templateId, array $mergeTags, array $recipients): void
    {
        $email = new Email();
        $email
            ->text('Unnecessary text')
            ->sender(new Address('l.merotta@gmail.com', 'Projectify'));

        foreach ($recipients as $address => $name) {
            $email->addTo(new Address($address, $name));
        }

        $email->getHeaders()
            ->addTextHeader('templateId', $templateId)
            ->addParameterizedHeader('params', 'params', $mergeTags);

        $this->mailer->send($email);
    }

    #[Required]
    public function setMailer(MailerInterface $mailer): void
    {
        $this->mailer = $mailer;
    }
}
