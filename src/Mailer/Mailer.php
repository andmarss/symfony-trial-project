<?php

namespace App\Mailer;

use App\Entity\User;

class Mailer
{
    /**
     * @var \Swift_Mailer $mailer
     */
    private $mailer;
    /**
     * @var \Twig\Environment $twig
     */
    private $twig;
    /**
     * @var string
     */
    private $mailFrom;

    /**
     * Mailer constructor.
     * @param \Swift_Mailer $mailer
     * @param \Twig\Environment $twig
     * @param string $mailFrom
     */
    public function __construct(\Swift_Mailer $mailer, \Twig\Environment $twig, string $mailFrom)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->mailFrom = $mailFrom;
    }

    public function send(User $user)
    {
        $body = $this->twig->render('email/registration.html.twig', [
            'user' => $user
        ]);

        $message = new \Swift_Message();

        $message
            ->setFrom($this->mailFrom)
            ->setSubject('Welcome to micro-post app!')
            ->setTo($user->getEmail())
            ->setBody($body, 'text/html');

        $this->mailer->send($message);
    }
}