<?php
/**
 * Created by PhpStorm.
 * User: stefano
 * Date: 14/07/18
 * Time: 11:21
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Commande;
use AppBundle\Form\CommandeType;
use AppBundle\Service\CalculerPrix;
use AppBundle\Service\LouvreConstraints;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends Controller
{
    /**
     * @Route("/louvre/panier", name="panier")
     */
    public function cartAction(Request $request, LouvreConstraints $louvreConstraints, CalculerPrix $calculerPrix)
    {
        $order = new Commande();
        $form = $this->createForm(CommandeType::class, $order);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            if ($louvreConstraints->ticketAvailable($order)){
                $user = $request->getSession()->get('User');
                $order->setUtilisateur($user);
                foreach ($order->getBillets() as $ticket){
                    $calculerPrix->prixBillet($ticket, $order);
                }
                $order->calculateOrderPrice();
                $request->getSession()->set('Order', $order);
                return $this->redirectToRoute('recap_cmd');
            }else{
                $this->addFlash('warning', "Désoler mais, la limite de billet pour cette date a été atteinte, merci de choisir une autre date de visite");
            }
        }
        return $this->render(':louvre:panier.html.twig', array('form' => $form->createView(), 'constraints' => $louvreConstraints));
    }
}