<?php
/**
 * Created by PhpStorm.
 * User: stefa
 * Date: 10/12/2017
 * Time: 18:34
 */

namespace AppBundle\Service;

use AppBundle\Service\JoursFeries;
use AppBundle\Entity\Commande;
use AppBundle\Repository\CommandesRepository;
use Doctrine\ORM\EntityManager;


class EstDisponible
{
    public $jourFeries;
    protected $em;


    const BILLET_MAX = 1000;
    const JOUR_FERME = array(2,7) ; // mardi et dimanche
    const HEURE_LIMITE_JOURNEE = 14;

    public function __construct(JoursFeries $joursFeries, EntityManager $em)
    {
        $this->jourFeries = $joursFeries;
        $this->em = $em;

    }

    public function getDate()
    {
        $date = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
        return $date;
    }

    public function billetsDispo(Commande $commande)
    {
        $commandeRepo = $this->em->getRepository(Commande::class);
        $nbBillets = $commandeRepo->countBillets($commande);
        $dateBillet = $commande->getDateBillet()->format('dd-mm-Y');
        $dateJour = $this->getDate()->format('dd-mm-Y');

        if ($nbBillets >= self::BILLET_MAX OR $dateBillet < $dateJour )
        {
            return false;
        }
        elseif ($this->dateIsOpen($dateBillet))
        {
            return false;
        }

        return true;
    }

    public function resteBillets()
    {
            return "Désoler mais, il ne reste plus de billets pour cette date";
    }

    public function dateIsOpen($date)
    {
        $joutSemaine = date('N', strtotime($date));
        $jourFeries = [$this->jourFeries->jours_feries()];
        $isHoliday = in_array($date, $jourFeries);

        if(in_array($joutSemaine,self::JOUR_FERME) OR $isHoliday == true)
        {
            return true;
        }
        return false;
    }

    public function dateLimite()
    {
        $dateLimite = $this->getDate()->add(new \DateInterval('P6M'));
        return $dateLimite->format('d-m-Y');
    }







}