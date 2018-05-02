<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Commande;
use AppBundle\Entity\Utilisateur;
use AppBundle\Form\CommandeType;
use AppBundle\Form\UtilisateurType;
use AppBundle\Service\EstDisponible;
use AppBundle\Service\GestionCommande;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class LouvreController extends Controller
{

    /**
     * @Route("/", name="accueil")
     */
    public function accueilAction(GestionCommande $commande)
    {
        $commande->remove();

        return $this->render(':louvre:accueil.html.twig');
    }

    /**
     * @Route("/louvre/info_facturation", name="info_fac")
     */
    public function infoFacturationAction(Request $request, GestionCommande $gestionCommande)
    {
        $utilisateur = new Utilisateur();
        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        if ($request->isMethod('POST'))
        {
            $form->handleRequest($request);
            if ($form->isValid())
            {
                $gestionCommande->setUtilisateur($utilisateur);
                return $this->redirectToRoute('panier');
            }
        }
        return $this->render(':louvre:infoFacturation.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/louvre/panier", name="panier")
     */
    public function panierAction(Request $request, EstDisponible $estDisponible, GestionCommande $gestionCommande)
    {
        $commande = new Commande();
        $form = $this->createForm(CommandeType::class, $commande);
        if ($request->isMethod('POST')){
            if ($form->handleRequest($request)->isValid()){
                $commande->setUtilisateur($gestionCommande->getUtilisateur());
                $gestionCommande->setCommande($commande);
                return $this->redirectToRoute('recap_cmd');
            }
        }
        return $this->render(':louvre:panier.html.twig', array('form' => $form->createView(), 'estDispo' => $estDisponible));
    }

    /**
     * @Route("/louvre/recap", name="recap_cmd")
     */
    public function recapAction(Request $request, GestionCommande $gestionCommande)
    {

        $commande = $gestionCommande->getCommande();

        if ($request->isMethod('POST'))
        {
            if (!$request->request->has('stripeToken'))
            {
                return new BadRequestHttpException("il n'y a pas de token Stripe pour créer le payement");
            }
            $token = $request->request->get("stripeToken");
            $gestionCommande->payment($token);


            $mailer = $this->get('mailer');
            $message = (new \Swift_Message('Louvre'))
                ->setTo($commande->getUtilisateur()->getEmail())
                ->setBody(
                    $this->renderView(
                        ':louvre/mail:mail.html.twig', array('commande' => $commande)
                    ),
                    'text/html'
                );
            $mailer->send($message);
            $this->addFlash('success', 'Votre commande a bien été validée');
            return $this->redirectToRoute('validation_cmd');
        }
        return $this->render(':louvre:recapPanier.html.twig', array('commande' => $commande));
    }

    /**
     * @Route("/louvre/validation", name="validation_cmd")
     */
    public function validationCommandeAction()
    {
        return $this->render(':louvre:validation.html.twig');
    }

    /**
     * @Route("louvre/test")
     */
    public function testAction()
    {
        $mailer = $this->get('mailer');
        $message = (new \Swift_Message('Louvre'))
            ->setTo('stefano0012@gmail.com')
            ->setBody(

                $this->renderView(
                    'base.html.twig'
                ),
                'text/html'
            );
        $mailer->send($message);
        $this->addFlash('success', 'Votre commande a bien été validée');

    }


}