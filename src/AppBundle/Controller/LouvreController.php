<?php
/**
 * Created by PhpStorm.
 * User: stefa
 * Date: 16/10/2017
 * Time: 18:57
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Billet;
use AppBundle\Entity\Commande;
use AppBundle\Entity\Utilisateur;
use AppBundle\Form\BilletType;
use AppBundle\Form\CommandeType;
use AppBundle\Form\UtilisateurType;
use AppBundle\Repository\CommandesRepository;
use AppBundle\Service\EstDisponible;
use AppBundle\Service\JoursFeries;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Service\CalculerPrix;
use Symfony\Component\HttpFoundation\Response;

class LouvreController extends Controller
{
    private function em()
    {
        $em = $this->getDoctrine()->getManager();
        return $em;
    }

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
    public function panierAction(Request $request, $idUser)
    {
        $commande = new Commande();
        $utilisateur = self::em()->getRepository("AppBundle:Utilisateur")->find($idUser);
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
                    self::em()->persist($billet);
                    $total = $total + $prix;
                }
                $commande->setPrix($total);
                self::em()->persist($commande);
                $commandeRepo = self::em()->getRepository("AppBundle:Commande");
                $billetsDispo = $commandeRepo->countBillets($commande);
                $estDisponible = new EstDisponible();
                if ($estDisponible->billetsDispo($billetsDispo) == true)
                {
                    self::em()->flush();
                    $this->addFlash('success', 'Billet bien enregistré.');
                    return $this->redirectToRoute('recap_cmd', array('idCmd' => $commande->getId()));
                }
                else
                {
                    $infoDispo = $estDisponible->resteBillets($billetsDispo);
                    $this->addFlash('danger', $infoDispo);
                    return $this->render(':louvre:panier.html.twig', array('form' => $form->createView()));
                }
            }
        }
        return $this->render(':louvre:panier.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/louvre/info_facturation", name="info_fac")
     */
    public function infoFacturationAction(Request $request)
    {
        $utilisateur = new Utilisateur();
        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        if ($request->isMethod('POST'))
        {
            $form->handleRequest($request);
            if ($form->isValid())
            {
                self::em()->persist($utilisateur);
                self::em()->flush();
                return $this->redirectToRoute('panier', array('idUser' => $utilisateur->getId()));
            }
        }

        return $this->render(':louvre:infoFacturation.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/louvre/recap/commande:{idCmd}", name="recap_cmd")
     */
    public function recapAction(Request $request, $idCmd, \Swift_Mailer $mailer, EntityManager $em)
    {
        $commandeRepo = $em->getRepository('AppBundle:Commande');
        $commande = $commandeRepo->find($idCmd);
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
            $message = (new \Swift_Message('Hello Email'))
                ->setFrom('stefano0012@gmail.com')
                ->setTo('stef.tcr@gmail.com')
                ->setBody(
                    $this->renderView(
                        ':louvre:mail.html.twig'
                        ),
                        'text/html'
                    );
            $mailer->send($message);
            return $this->render("louvre/paiement.html.twig");
        }
        return $this->render(':louvre:recapPanier.html.twig', array('commande' => $commande));
    }

    /**
     * @Route("/louvre/test", name="test")
     */
    public function testAction(\Swift_Mailer $mailer, EntityManager $em, Request $request, JoursFeries $joursFeries)
    {

         $message = (new \Swift_Message('Hello Email'))
            ->setFrom('stefano0012@gmail.com')
            ->setTo('stefano0012@gmail.com')
            ->setBody(
                $this->renderView(
                    ':louvre:mail.html.twig'
                ),
                'text/html'
            );
        $mailer->send($message);


    /**
        $message = $this->renderView('louvre/mail.html.twig', array('test' => 'test'));
        $headers = 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

        mail('stefano0012@gmail.com', 'symfony', $message, $headers);
    **/
        if ($request->isMethod('POST'))
        {
            $date = $request->request->get('date');
            $joursFeries->test($date);
            var_dump($joursFeries);

        }
            return $this->render("louvre/paiement.html.twig");
    }

    /**
     * @Route("/louvre/paiement")
     */
    public function paiementAction(Request $request)
    {

            return $this->render("louvre/paiement.html.twig");

    }


}