<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Commande;
use AppBundle\Service\EstDisponible;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\Response;

class TestLouvreController extends Controller
{
    /**
     * @Route("/louvre/test", name="test")
     */
    public function testAction()
    {
        $commande = $this->getDoctrine()->getManager()->getRepository('AppBundle:Commande')->find(49);
        return $this->render('louvre\mail\mail.html.twig', array('commande' => $commande));
        /**
        $mailer = $this->get('mailer');
        $message = (new \Swift_Message('Hello Email'))
            ->setFrom('stefano0012@gmail.com')
            ->setTo('stefano0012@gmail.com')
            ->setBody(
                $this->renderView(
                    ':louvre/mail:mail.html.twig'
                ),
                'text/html'
            );
        $mailer->send($message);
         **/


        /**
        $message = $this->renderView('louvre/mail.html.twig', array('test' => 'test'));
        $headers = 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

        mail('stefano0012@gmail.com', 'symfony', $message, $headers);

        if ($request->isMethod('POST'))
        {
            $date = $request->request->get('date');
            if($estDisponible->dateIsOpen($date))
            {
                return 'pas bon :';
            }
            return 'Ok :';
        }
        return $this->render("louvre/paiement.html.twig", array('estDispo' => $estDisponible));
        **/
    }

    /**
     * @Route("/louvre/paiement")
     */
    public function paiementAction()
    {
        $mailer = $this->get('mailer');
        $message = (new \Swift_Message('Hello Email'))
            ->setFrom('stefano0012@gmail.com')
            ->setTo('stefano0012@gmail.com')
            ->setBody(
                $this->renderView(
                    ':louvre/mail:mail.html.twig'
                ),
                'text/html'
            );
        $mailer->send($message);
        return new Response('<p>ok</p>');

    }
}
