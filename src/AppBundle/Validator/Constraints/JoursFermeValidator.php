<?php


namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class JoursFermeValidator extends ConstraintValidator
{
    private $joursFerme = ['2','7']; // mardi et dimanche

    public function validate($value, Constraint $constraint)
    {
        $date = $value->format('d-m-Y');
        $joutSemaine = date('N', strtotime($date));

        if(in_array($joutSemaine, $this->joursFerme))
        {
            $this->context
                ->buildViolation($constraint->messageJoursFermes)
                ->addViolation()
            ;
        }

        if (in_array($value->format('d-m-Y'), $this->getNationalHolidays($value)))
        {
            $this->context
                ->buildViolation($constraint->messageJoursFeries)
                ->addViolation()
            ;
        }
    }

    private function getEasterDatetime($year)
    {
        $base = new \DateTime("$year-03-21");
        $days = easter_days($year);
        $base->add(new \DateInterval("P{$days}D"));
        return $base->format('d-m-Y');
    }

    private function getNationalHolidays(\DateTime $date)
    {
        $year = $date->format('Y');

        $dimanchePaques = $this->getEasterDatetime($year);
        $lundiPaques = date("d-m-Y", strtotime("$dimanchePaques +1 day"));
        $jeudiAscension = date("d-m-Y", strtotime("$dimanchePaques +39 day"));
        $lundiPentecote = date("d-m-Y", strtotime("$dimanchePaques +50 day"));
        $joursFeries = array
        (
            $dimanchePaques,
            $lundiPaques
        ,   $jeudiAscension
        ,   $lundiPentecote

        ,    "01-01-$year"         //    Nouvel an
        ,    "01-05-$year"         //    Fête du travail
        ,    "08-05-$year"        //    Armistice 1945
        ,    "14-07-$year"         //    Fête nationale
        ,    "15-08-$year"         //    Assomption
        ,    "01-11-$year"         //    Toussaint
        ,    "11-11-$year"         //    Armistice 1918
        ,    "25-12-$year"         //    Noël
        );
        ;
        return $joursFeries;
    }
}