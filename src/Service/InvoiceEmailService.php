<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class InvoiceEmailService
{
    private MailerInterface $mailer;
    private Environment $twig;

    public function __construct(MailerInterface $mailer, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function sendInvoiceEmail(User $user, array $invoiceData, string $plan): void
    {
        $email = (new Email())
            ->from('noreply@foliozen.com')
            ->to($user->getEmail())
            ->subject('Facture FolioZen - Abonnement ' . ucfirst($plan))
            ->html($this->twig->render('emails/invoice.html.twig', [
                'user' => $user,
                'invoice' => $invoiceData,
                'plan' => $plan,
            ]));

        $this->mailer->send($email);
    }

    public function sendWelcomeEmail(User $user, string $plan): void
    {
        $email = (new Email())
            ->from('noreply@foliozen.com')
            ->to($user->getEmail())
            ->subject('Bienvenue sur FolioZen ' . ucfirst($plan) . ' !')
            ->html($this->twig->render('emails/welcome.html.twig', [
                'user' => $user,
                'plan' => $plan,
            ]));

        $this->mailer->send($email);
    }
}
