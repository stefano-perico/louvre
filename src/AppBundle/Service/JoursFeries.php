<?php


namespace AppBundle\Service;



class JoursFeries
{
    private $annee;

    public function __construct()
    {
        $this->annee = date('Y');
    }

    protected function getEasterDatetime()
    {
        $year = $this->annee;
        $base = new \DateTime("$year-03-21");
        $days = easter_days($year);

        $base->add(new \DateInterval("P{$days}D"));
        return $base->format('d-m-Y');
    }

    public function joursFeries(\DateTime $date = null)
    {
        if ($date == null)
        {
            $annee = $this->annee;
        }
        else
        {
            $annee = $date->format('Y');
        }

        $dimanchePaques = $this->getEasterDatetime();
        $lundiPaques = date("d-m-Y", strtotime("$dimanchePaques +1 day"));
        $jeudiAscension = date("d-m-Y", strtotime("$dimanchePaques +39 day"));
        $lundiPentecote = date("d-m-Y", strtotime("$dimanchePaques +50 day"));
        $joursFeries = array
        (
            $dimanchePaques,
            $lundiPaques
        ,   $jeudiAscension
        ,   $lundiPentecote

        ,    "01-01-$annee"         //    Nouvel an
        ,    "01-05-$annee"         //    Fête du travail
        ,    "08-05-$annee"        //    Armistice 1945
        ,    "14-07-$annee"         //    Fête nationale
        ,    "15-08-$annee"         //    Assomption
        ,    "01-11-$annee"         //    Toussaint
        ,    "11-11-$annee"         //    Armistice 1918
        ,    "25-12-$annee"         //    Noël
        );
        ;
        return $joursFeries;
    }

    public function getJoursFeriesDatePicker(){
        return implode(",", $this->joursFeries());
    }




}