<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Billet;
use AppBundle\Entity\Commande;
use AppBundle\Form\BilletType;
use AppBundle\Form\CommandeType;
use AppBundle\Service\EstDisponible;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\Response;

class TestLouvreController extends Controller
{
    /**
     * @Route("/test", name="test")
     */
    public function testAction()
    {
        $billet = new Commande();
        $form = $this->createForm(CommandeType::class, $billet);
        return $this->render(':louvre/form:panierTest.html.twig', ['form' => $form->createView()]);
    }

}
