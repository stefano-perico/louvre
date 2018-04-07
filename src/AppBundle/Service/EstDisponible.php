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
    const JOURS_SEMAINE = array('lundi' => 1, 'mardi' => 2, 'mercredi' => 3, 'jeudi' => 4, 'vendredi' => 5, 'samedi' => 6, 'dimanche' => 7);

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

        if($joutSemaine == self::JOURS_SEMAINE['mardi'] OR $joutSemaine == self::JOURS_SEMAINE['dimanche'] OR $isHoliday == true)
        {
            return true;
        }
        return false;
    }






}