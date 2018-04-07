<?php
/**
 * Created by PhpStorm.
 * User: stefa
 * Date: 16/10/2017
 * Time: 18:57
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Commande;
use AppBundle\Entity\Utilisateur;
use AppBundle\Form\CommandeType;
use AppBundle\Form\UtilisateurType;
use AppBundle\Service\EstDisponible;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Service\CalculerPrix;

class LouvreController extends Controller
{
 

    /**
     * @Route("/")
     */
    public function accueilAction()
    {
        return $this->render(':louvre:accueil.html.twig');
    }

    /**
     * @Route("/louvre/panier/utilisateur:{idUser}", name="panier")
     */
    public function panierAction(Request $request, EstDisponible $estDisponible, $idUser)
    {
        $commande = new Commande();
        $em = $this->getDoctrine()->getManager();
        $utilisateur = $em->getRepository("AppBundle:Utilisateur")->find($idUser);
        $form = $this->createForm(CommandeType::class, $commande);

        if ($request->isMethod('POST'))
        {
            $form->handleRequest($request);
            if ($form->isValid())
            {
                $commande->setUtilisateur($utilisateur);
                $total = 0;
                foreach ($commande->getBillet() as $billet)
                {
                    $calculerPrix = new CalculerPrix();
                    $prix = $calculerPrix->prixBillet($billet);
                    $billet->setCommande($commande);
                    $em->persist($billet);
                    $total = $total + $prix;
                }
                $commande->setPrix($total);
                $em->persist($commande);
                $commandeRepo = $em->getRepository("AppBundle:Commande");
                $estDisponible = $this->get('app.est_disponible:');
                if ($estDisponible->billetsDispo($commande, $commandeRepo) == true)
                {
                    $em->flush();
                    $this->addFlash('success', 'Billets bien enregistré.');
                    return $this->redirectToRoute('recap_cmd', array('idCmd' => $commande->getId()));
                }
                else
                {
                    $infoDispo = $estDisponible->resteBillets();
                    $this->addFlash('danger', $infoDispo);
                    return $this->render(':louvre:panier.html.twig', array('form' => $form->createView(), 'estDispo' => $estDisponible->jourFeries->jours_feries()));
                }
            }
        }
        return $this->render(':louvre:panier.html.twig', array('form' => $form->createView(), 'estDispo' => $estDisponible->jourFeries->jours_feries()));
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
     * @Route("/louvre/recap/commande:{idCmd}", name="recap_cmd")
     */
    public function recapAction(Request $request, $idCmd)
    {
        $em = $this->getDoctrine()->getManager();
        $commande = $em->getRepository('AppBundle:Commande')->find($idCmd);
        if ($request->isMethod('POST'))
        {
            \Stripe\Stripe::setApiKey("sk_test_0qfLdJkutmeqc5EVVMWRQzI0");
            // Token is created using Checkout or Elements!
            // Get the payment token ID submitted by the form:
            $token = $request->request->get("stripeToken");
            $prix = $commande->caluclerPrixCentimes();
            // Charge the user's card:
            \Stripe\Charge::create(array(
                "amount" => $prix,
                "currency" => "eur",
                "description" => "Example charge",
                "source" => $token,
            ));
            $mailer = $this->get('mailer');
            $message = (new \Swift_Message('Hello Email'))
                ->setFrom('stefano0012@gmail.com')
                ->setTo('stefano0012@gmail.com')
                ->setBody(
                    $this->renderView(
                        ':louvre/mail:mail.html.twig', array('commande' => $commande)
                    ),
                    'text/html'
                );
            $mailer->send($message);
        }
        return $this->render(':louvre:recapPanier.html.twig', array('commande' => $commande));
    }


}