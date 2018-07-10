<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Commande;
use AppBundle\Entity\Utilisateur;
use AppBundle\Form\CommandeType;
use AppBundle\Form\UtilisateurType;
use AppBundle\Repository\CommandesRepository;
use AppBundle\Service\CalculerPrix;
use AppBundle\Service\EstDisponible;
use AppBundle\Service\GestionCommande;
use AppBundle\Service\StripeService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class LouvreController extends Controller
{

    /**
     * @Route("/", name="accueil")
     */
    public function accueilAction()
    {
        return $this->render(':louvre:accueil.html.twig');
    }

    /**
     * @Route("/louvre/info_facturation", name="info_fac")
     */
    public function infoFacturationAction(Request $request)
    {
        $utilisateur = new Utilisateur();
        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $request->getSession()->set('User', $utilisateur);
            $this->addFlash('success', 'Vos informations de facturations ont bien été enregistrées');
            return $this->redirectToRoute('panier');
        }
        return $this->render(':louvre:infoFacturation.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/louvre/panier", name="panier")
     */
    public function panierAction(Request $request, EstDisponible $estDisponible, CalculerPrix $calculerPrix)
    {
        $order = new Commande();
        $form = $this->createForm(CommandeType::class, $order);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $user = $request->getSession()->get('User');
            $order->setUtilisateur($user);
            foreach ($order->getBillets() as $ticket)
            {
                $calculerPrix->prixBillet($ticket, $order);
            }
            $order->calculateOrderPrice();
            $request->getSession()->set('Order', $order);
            return $this->redirectToRoute('recap_cmd');
        }
        return $this->render(':louvre:panier.html.twig', array('form' => $form->createView(), 'estDispo' => $estDisponible));
    }

    /**
     * @Route("/louvre/recap", name="recap_cmd")
     */
    public function recapAction(Request $request, \Swift_Mailer $mailer, StripeService $stripe)
    {
        $em = $this->getDoctrine()->getManager();
        $order = $request->getSession()->get('Order');
        $token = $this->container->getParameter('stripe_public_key');
        if ($request->isMethod('POST')){
            $price = $order->calculatePriceInCent();
            $stripe->charge($request->request->get('stripeToken'), $price);
            if (!empty($stripe->getErrors()))
            {
                foreach ($stripe->getErrors() as $error)
                {
                    $this->addFlash('danger', $error);
                }
            }
            else{
                $order->setValide(true);
                $this->addFlash('success', 'Votre commande a bien été validée');
                if ($order->getValide()){
                    $message = (new \Swift_Message('Louvre'))
                        ->setTo($order->getUtilisateur()->getEmail())
                        ->setBody(
                            $this->renderView(':louvre/mail:mail.html.twig', array('commande' => $order)),
                            'text/html'
                        );
                    $em->persist($order);
                    $em->flush();
                    $mailer->send($message);
                    return $this->redirectToRoute('validation_cmd');
                }
            }
        }
        return $this->render(':louvre:recapPanier.html.twig', array('commande' => $order, 'token' => $token));
    }

    /**
     * @Route("/louvre/validation", name="validation_cmd")
     */
    public function validationCommandeAction(GestionCommande $commande)
    {
        $commande->remove();
        return $this->render(':louvre:validation.html.twig');
    }

}