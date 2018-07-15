<?php
/**
 * Created by PhpStorm.
 * User: stefa
 * Date: 10/12/2017
 * Time: 18:34
 */

namespace AppBundle\Service;

use AppBundle\Entity\Commande;
use Doctrine\ORM\EntityManager;


class LouvreConstraints
{
    const BILLET_MAX = 1000;
    const HEURE_LIMITE_JOURNEE = 14;

    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getDate()
    {
        $date = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
        return $date;
    }

    public function ticketAvailable(Commande $commande)
    {
        $commandeRepo = $this->em->getRepository(Commande::class);
        $nbBillets = $commandeRepo->countBillets($commande);
        $dateBillet = $commande->getDateBillet()->format('d-m-Y');
        $dateJour = $this->getDate()->format('d-m-Y');

        if ($nbBillets >= self::BILLET_MAX OR $dateBillet < $dateJour )
        {
            return false;
        }

        return true;
    }

    public function deadline()
    {
        $dateLimite = $this->getDate()->add(new \DateInterval('P6M'));
        return $dateLimite->format('d-m-Y');
    }








}