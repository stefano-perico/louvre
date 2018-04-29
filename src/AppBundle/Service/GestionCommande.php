<?php


namespace AppBundle\Service;


use AppBundle\Entity\Commande;
use AppBundle\Entity\Utilisateur;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\Session;

class GestionCommande
{
    private $em;
    private $session;


    public function __construct(EntityManager $em, Session $session)
    {
        $this->em = $em;
        $this->session = $session;
    }


    public function getCommande()
    {
        if (!$this->session->get('commande'))
        {
            $this->setCommande(new Commande);
        }
        return $this->session->get('commande');
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


}