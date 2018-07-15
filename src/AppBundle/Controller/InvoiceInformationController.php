<?php
/**
 * Created by PhpStorm.
 * User: stefano
 * Date: 14/07/18
 * Time: 11:19
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Utilisateur;
use AppBundle\Form\UtilisateurType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class InvoiceInformationController extends Controller
{
    /**
     * @Route("/louvre/info_facturation", name="info_fac")
     */
    public function invoiceInformationAction(Request $request)
    {
        $user = new Utilisateur();
        $form = $this->createForm(UtilisateurType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $request->getSession()->set('User', $user);
            $this->addFlash('success', 'Vos informations de facturations ont bien été enregistrées');
            return $this->redirectToRoute('panier');
        }
        return $this->render(':louvre:infoFacturation.html.twig', array('form' => $form->createView()));
    }
}