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
use AppBundle\Entity\UtilisateurAdresse;
use AppBundle\Form\BilletType;
use AppBundle\Form\CommandeType;
use AppBundle\Form\UtilisateurAdresseType;
use AppBundle\Form\UtilisateurType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LouvreController extends Controller
{
    /**
     * @Route("/louvre")
     */
    public function accueilAction()
    {
        return $this->render(':louvre:accueil.html.twig');
    }

    /**
     * @Route("/louvre/selection")
     */
    public function selectionAction()
    {

        return $this->render(':louvre:selection.html.twig');
    }

    /**
     * @Route("/louvre/panier/utilisateur:{idUser}", name="panier")
     */
    public function panierAction(Request $request, $idUser)
    {
        $em = $this->getDoctrine()->getManager();
        $utilisateur = $em->getRepository("AppBundle:Utilisateur")->find($idUser);
        $commande = new Commande();
        $form = $this->createForm(CommandeType::class,$commande);

        if ($request->isMethod('POST'))
        {
            $form->handleRequest($request);
            if ($form->isValid())
            {
                $commande->setUtilisateur($utilisateur);
                foreach ($commande->getBillet() as $billet)
                {
                    $billet->setCommande($commande);
                    $em->persist($billet);
                }
                $em->persist($commande);
                $em->flush();

                $request->getSession()->getFlashBag()->add('notice', 'Billet bien enregistré.');
                return $this->redirectToRoute('recap_cmd', array('idCmd' => $commande->getId(), 'idUser' => $idUser));
            }
        }
        return $this->render(':louvre:panier.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/louvre/info_facturation, name="info_fac")
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
                $em = $this->getDoctrine()->getManager();
                $em->persist($utilisateur);
                $em->flush();
                return $this->redirectToRoute('panier', array('idUser' => $utilisateur->getId()));
            }
        }

        return $this->render(':louvre:infoFacturation.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/louvre/recap/commande:{idCmd}/utilisateur:{idUser}/adresse:{idAdresse}", name="recap_cmd")
     */
    public function recapAction(Request $request, $idCmd, $idUser, $idAdresse)
    {
        $repository = $this
            ->getDoctrine()
            ->getManager()
        ;

        $utilisateur = $repository->getRepository('AppBundle:Utilisateur')->find($idUser);
        $utilisateurAdresse = $repository->getRepository('AppBundle:UtilisateurAdresse')->find($idAdresse);
        $commande = $repository->getRepository('AppBundle:Commande')->find($idCmd);

        return $this->render(':louvre:recapPanier.html.twig', array('commande' => $commande, 'utilisateurAdresse' => $utilisateurAdresse ));
    }

    /**
     * @Route("/louvre/payement/commande:{id}")
     */
    public function payementAction()
    {
        return $this->render(':louvre:payement.html.twig');
    }

    public function addAction(Request $request)
    {
        if ($request->isMethod('POST'))
        {

        }


    }

}