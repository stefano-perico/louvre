<?php


namespace AppBundle\Service;


class MailerService
{
    public function sendMail($to, $key, \Swift_Mailer $mailer)
    {
        $message = (new \Swift_Message('Louvre'))
            ->setTo($to)
            ->setBody(
                $this->renderView(
                    ':louvre/mail:mail.html.twig', array('"'.$key.'"' => $key)
                ),
                'text/html'
            );
        $mailer->send($message);
    }
}