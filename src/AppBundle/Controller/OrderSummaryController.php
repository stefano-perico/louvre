<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Commande;
use AppBundle\Repository\CommandesRepository;
use AppBundle\Service\MailerService;
use AppBundle\Service\StripeService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class OrderSummaryController extends Controller
{
    /**
     * @Route("/louvre/recap", name="recap_cmd")
     */
    public function orderSummaryAction(Request $request, MailerService $mailer, StripeService $stripe)
    {
        $em = $this->getDoctrine()->getManager();
        $order = $request->getSession()->get('Order');
        if ($request->isMethod('POST')){
            $stripe->charge($request->request->get('stripeToken'), $order->calculatePriceInCent());
            if (!empty($stripe->getErrors())){
                foreach ($stripe->getErrors() as $error){
                    $this->addFlash('danger', $error);
                }
            }
            else{
                $order->setValide(true);
                $this->addFlash('success', 'Votre commande a bien été validée');
                $mailer->sendMessage($order);
                $em->persist($order);
                $em->flush();
                return $this->redirectToRoute('validation_cmd');
            }
        }
        return $this->render(':louvre:recapPanier.html.twig', array('commande' => $order, 'token' => $this->container->getParameter('stripe_public_key')));
    }

    /**
     * @Route("/louvre/validation", name="validation_cmd")
     */
    public function validationCommandeAction()
    {
        return $this->render(':louvre:validation.html.twig');
    }

}