<?php

namespace App\Service;

use App\Entity\Event;
use App\Entity\User;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class NotificationManager implements NotificationManagerInterface
{

    private PHPMailer $mailer;
    private Environment $twig;

    public function __construct(Environment $twig) {
        $this->twig = $twig;
    }

    protected function initMailer(): void
    {
        $this->mailer = new PHPMailer(true);
        $this->mailer->isSMTP();
        $this->mailer->Host = $_ENV['MAILER_HOST'];
        $this->mailer->Port = $_ENV['MAILER_PORT'];
        $this->mailer->SMTPAuth = $_ENV['MAILER_SMTP_AUTH'] === 'true';
        $this->mailer->Username = $_ENV['MAILER_USERNAME'] ?: null;
        $this->mailer->Password = $_ENV['MAILER_PASSWORD'] ?: null;
    }

    /**
     * @throws Exception
     */
    protected function sendEmail(string $to, string $subject, string $body): void
    {
        $this->initMailer();

        $this->mailer->setFrom($_ENV['MAILER_FROM_ADDRESS'], $_ENV['MAILER_FROM_NAME']);
        $this->mailer->addAddress($to);
        $this->mailer->Subject = $subject;
        $this->mailer->Body = $body;
        $this->mailer->isHTML();
        $this->mailer->CharSet = 'UTF-8';
        $this->mailer->send();
    }

    /**
     * @throws Exception
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function sendRegistrationConfirmation(User $to, Event $event): void
    {
        $subject = 'Confirmation d\'inscription : ' . $event->getName();
        $body = $this->twig->render('mails/registration_confirmation.html.twig', [
            'user' => $to,
            'event' => $event,
        ]);
        $this->sendEmail($to->getEmail(), $subject, $body);
    }

    /**
     * @throws Exception
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function sendRegistrationCancellation(User $to, Event $event): void
    {
        $subject = 'Annulation d\'inscription : ' . $event->getName();
        $body = $this->twig->render('mails/registration_cancellation.html.twig', [
            'user' => $to,
            'event' => $event,
        ]);
        $this->sendEmail($to->getEmail(), $subject, $body);
    }

}