<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Commande;
use AppBundle\Entity\Utilisateur;
use AppBundle\Form\CommandeType;
use AppBundle\Form\UtilisateurType;
use AppBundle\Service\EstDisponible;
use AppBundle\Service\GestionCommande;
use AppBundle\Service\StripeService;
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
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $gestionCommande->setUtilisateur($utilisateur);
            $this->addFlash('success', 'Vos informations de facturations ont bien été enregistrées');
            return $this->redirectToRoute('panier');
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
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $commande->setUtilisateur($gestionCommande->getUtilisateur());
            $gestionCommande->setCommande($commande);
            return $this->redirectToRoute('recap_cmd');
        }
        return $this->render(':louvre:panier.html.twig', array('form' => $form->createView(), 'estDispo' => $estDisponible));
    }

    /**
     * @Route("/louvre/recap", name="recap_cmd")
     */
    public function recapAction(Request $request, GestionCommande $gestionCommande, \Swift_Mailer $mailer)
    {
        $em = $this->getDoctrine()->getManager();
        $commande = $gestionCommande->getCommande();
        if ($request->isMethod('POST'))
        {
            if (!$request->request->has('stripeToken'))
            {
                return new BadRequestHttpException("il n'y a pas de token Stripe pour créer le payement");
            }
            $token = $request->request->get("stripeToken");
            $gestionCommande->payment($token);
            if ($commande->getValide() == true)
            {
                $message = (new \Swift_Message('Louvre'))
                    ->setTo($commande->getUtilisateur()->getEmail())
                    ->setBody(
                        $this->renderView(':louvre/mail:mail.html.twig', array('commande' => $commande)),
                        'text/html'
                    );
                $em->persist($commande);
                $em->flush();
                $mailer->send($message);
                return $this->redirectToRoute('validation_cmd');
            }
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
    public function testAction(StripeService $stripe, Request $request)
    {

        $prix = 100;
        if ($request->isMethod('POST'))
        {
            if (!$request->request->has('stripeToken'))
            {
                return new BadRequestHttpException("il n'y a pas de token Stripe pour créer le payement");
            }
            $token = $request->request->get('stripeToken');
            try
            {
                $charge = \Stripe\Charge::create([
                    "amount" => $prix,
                    "currency" => "eur",
                    "description" => "Example charge",
                    "source" => $token,
                ]);
                $this->addFlash('success', 'Votre commande a bien été validée');
            }
            catch (\Stripe\Error\Card $exception) {
                $body = $exception->getJsonBody();
                $err = $body['error'];
                $this->addFlash('danger', $err['type'] . ' ' . $err['code'] . ' ' . $err['message']);
            }
            catch (\Stripe\Error\RateLimit $exception){

            }
            catch (\Stripe\Error\InvalidRequest $exception){

            }
            catch (\Stripe\Error\Authentication $exception){

            }
            catch (\Stripe\Error\ApiConnection $exception){

            }
            catch (\Stripe\Error\Base $exception){

            }
            catch (\Exception $exception){

            }

        }
       return $this->render(':louvre:test.html.twig', array('prix' => $prix));
    }


}