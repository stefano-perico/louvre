<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Commande;
use AppBundle\Entity\Utilisateur;
use AppBundle\Form\CommandeType;
use AppBundle\Form\UtilisateurType;
use AppBundle\Service\EstDisponible;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Service\CalculerPrix;

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
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        if ($request->isMethod('POST'))
        {
            $form->handleRequest($request);
            if ($form->isValid())
            {
                $em->persist($utilisateur);
                $em->flush();
                return $this->redirectToRoute('panier', array('idUser' => $utilisateur->getId()));
            }
        }
        return $this->render(':louvre:infoFacturation.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/louvre/panier/utilisateur:{idUser}", name="panier")
     */
    public function panierAction(Request $request, EstDisponible $estDisponible, CalculerPrix $calculerPrix, $idUser)
    {
        $commande = new Commande();
        $em = $this->getDoctrine()->getManager();
        $utilisateur = $em->getRepository(Utilisateur::class)->find($idUser);
        $form = $this->createForm(CommandeType::class, $commande);
        if ($request->isMethod('POST')){
            if ($form->handleRequest($request)->isValid()){
                $commande->setUtilisateur($utilisateur);
                $total = 0;
                foreach ($commande->getBillet() as $billet){
                    $prix = $calculerPrix->prixBillet($billet, $commande);
                    $billet->setCommande($commande);
                    $em->persist($billet);
                    $total = $total + $prix;
                }
                $commande->setPrix($total);
                $em->persist($commande);
                if ($estDisponible->billetsDispo($commande)){
                    $em->flush();
                    $this->addFlash('success', 'Billets bien enregistré.');
                    return $this->redirectToRoute('recap_cmd', array('idCmd' => $commande->getId(), 'idUser' => $idUser));
                }
                else{
                    $infoDispo = $estDisponible->resteBillets();
                    $this->addFlash('danger', $infoDispo);
                    return $this->render(':louvre:panier.html.twig', array('form' => $form->createView(), 'estDispo' => $estDisponible));
                }
            }
        }
        return $this->render(':louvre:panier.html.twig', array('form' => $form->createView(), 'estDispo' => $estDisponible));
    }

    /**
     * @Route("/louvre/recap/commande:{idCmd}/utilisateur:{idUser}", name="recap_cmd")
     */
    public function recapAction(Request $request, $idCmd, $idUser)
    {
        $em = $this->getDoctrine()->getManager();
        $commande = $em->getRepository(Commande::class)->find($idCmd);
        $utilisateur = $em->getRepository(Utilisateur::class)->find($idUser);
        if ($request->isMethod('POST'))
        {
            \Stripe\Stripe::setApiKey("sk_test_0qfLdJkutmeqc5EVVMWRQzI0");
            $token = $request->request->get("stripeToken");
            $prix = $commande->caluclerPrixCentimes();
            \Stripe\Charge::create(array(
                "amount" => $prix,
                "currency" => "eur",
                "description" => "Example charge",
                "source" => $token,
            ));
            $mailer = $this->get('mailer');
            $message = (new \Swift_Message('Louvre'))
                ->setTo($utilisateur->getEmail())
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


}