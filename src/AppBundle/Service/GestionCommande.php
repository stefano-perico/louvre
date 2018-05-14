<?php


namespace AppBundle\Service;


use AppBundle\Entity\Commande;
use AppBundle\Entity\Utilisateur;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class GestionCommande
{
    private $em;
    private $session;
    private $calculerPrix;
    private $stripe;


    public function __construct(EntityManager $em, Session $session, CalculerPrix $calculerPrix, StripeService $stripe)
    {
        $this->em = $em;
        $this->session = $session;
        $this->calculerPrix = $calculerPrix;
        $this->stripe = $stripe;

    }


    public function getCommande()
    {
        if (!$this->session->get('commande'))
        {
            $this->setCommande(new Commande);
        }
        $commande = $this->session->get('commande');
        $this->setPrix($commande);
        return $commande;
    }

    public function setCommande(Commande $commande)
    {
        $this->session->set('commande', $commande);
        return $this;
    }

    public function getUtilisateur()
    {
        return $this->session->get('utilisateur');
    }

    public function setUtilisateur(Utilisateur $utilisateur)
    {
        $this->session->set('utilisateur', $utilisateur);
        return $this;
    }

    public function remove()
    {
        $this->session->clear();
    }

    public function setPrix(Commande $commande)
    {
        $total = 0;
        foreach ($commande->getBillets() as $billet)
        {
            $prix = $this->calculerPrix->prixBillet($billet, $commande);
            $total = $total + $prix;
        }
        $commande->setPrix($total);
    }

    public function payment($token)
    {
        $prix = $this->getCommande()->caluclerPrixCentimes();
        $this->stripe->charge($token, $prix);
        if (!empty($this->stripe->getErrors()))
        {
            foreach ($this->stripe->getErrors() as $error)
            {
                $this->session->getFlashBag()->add('danger', $error);
            }
        }
        else
        {
            $commande = $this->getCommande();
            $commande->setValide(true);
            $this->session->getFlashBag()->add('success', 'Votre commande a bien été validée');
        }
    }

    public function sendMailCommande()
    {
        // TODO créer un fonction qui envoie le mail de confirmation de la commmande

    }


}