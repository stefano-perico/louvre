<?php


namespace AppBundle\Service;



class JoursFeries
{
    protected $annee;

    public function __construct()
    {
        $this->annee = date('Y');
    }

    protected function get_easter_datetime()
    {
        $year = $this->annee;
        $base = new \DateTime("$year-03-21");
        $days = easter_days($year);

        $base->add(new \DateInterval("P{$days}D"));
        return $base->format('d-m-Y');
    }

    public function jours_feries()
    {
        $annee = $this->annee;
        $dimanche_paques = $this->get_easter_datetime();
        $lundi_paques = date("d-m-Y", strtotime("$dimanche_paques +1 day"));
        $jeudi_ascension = date("d-m-Y", strtotime("$dimanche_paques +39 day"));
        $lundi_pentecote = date("d-m-Y", strtotime("$dimanche_paques +50 day"));
        $jours_feries = array
        (
            $dimanche_paques,
            $lundi_paques
        ,   $jeudi_ascension
        ,   $lundi_pentecote

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
        return implode(',', $jours_feries);
    }

    public function getJoursFeriesJson($key)
    {
        $test = in_array($key, $this->jours_feries());
        return $test;
    }



}