<?php


namespace AppBundle\Validator\Constraints;


use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class JoursFermeeValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        $year = $value->format('Y');
        $base = new \DateTime("$year-03-21");
        $days = easter_days($year);
        $base->add(new \DateInterval("P{$days}D"));

        $dimanche_paques = $base->format('d-m-Y');
        $lundi_paques = date("d-m-Y", strtotime("$dimanche_paques +1 day"));
        $jeudi_ascension = date("d-m-Y", strtotime("$dimanche_paques +39 day"));
        $lundi_pentecote = date("d-m-Y", strtotime("$dimanche_paques +50 day"));

        $jours_feries = array
        (
            $dimanche_paques,
            $lundi_paques
        ,   $jeudi_ascension
        ,   $lundi_pentecote

        ,    "01-01-$year"         //    Nouvel an
        ,    "01-05-$year"         //    Fête du travail
        ,    "08-05-$year"        //    Armistice 1945
        ,    "14-07-$year"         //    Fête nationale
        ,    "15-08-$year"         //    Assomption
        ,    "01-11-$year"         //    Toussaint
        ,    "11-11-$year"         //    Armistice 1918
        ,    "25-12-$year"         //    Noël
        );

        if (in_array($value->format('d-m-Y'), $jours_feries))
        {
            $this->context
                ->buildViolation($constraint->message)
                ->addViolation()
            ;
        }
    }
}