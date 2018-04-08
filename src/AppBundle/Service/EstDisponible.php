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



class EstDisponible
{
    public $jourFeries;

    const BILLET_MAX = 1000;
    const JOUR_FERME = array(2,7) ; // mardi et dimanche

    public function __construct(JoursFeries $joursFeries)
    {
        $this->jourFeries = $joursFeries;
    }

    public function billetsDispo(Commande $commande, CommandesRepository $commandesRepository)
    {
        $date = new \DateTime();
        $dateBillet = $commande->getDateBillet();
        if ($commandesRepository->countBillets($commande) >= self::BILLET_MAX OR $dateBillet < $date )
        {
            return false;
        }
        elseif ($this->dateIsOpen($dateBillet->format('d-m-Y')))
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
        $dateLimite = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
        $dateLimite->add(new \DateInterval('P6M'));
        return $dateLimite->format('d-m-Y');
    }






}