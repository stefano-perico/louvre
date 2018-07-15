<?php
/**
 * Created by PhpStorm.
 * User: stefano
 * Date: 15/07/18
 * Time: 12:55
 */

namespace AppBundle\Service;


use AppBundle\Entity\Commande;
use Symfony\Component\Templating\EngineInterface;

class MailerService
{
    private $mailer;
    private $templating;

    public function __construct(\Swift_Mailer $mailer, EngineInterface $templating)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
    }

    public function sendMessage(Commande $commande)
    {
        $message = (new \Swift_Message('Louvre'))
            ->setTo($commande->getUtilisateur()->getEmail())
            ->setBody(
                $this->templating->render(':louvre/mail:mail.html.twig', array('commande' => $commande)),
                'text/html'
            );
        $this->mailer->send($message);
    }





}