<?php


namespace AppBundle\Validator\Constraints;

use AppBundle\Service\JoursFeries;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class JoursFermeValidator extends ConstraintValidator
{
    protected $joursFerme = ['2','7']; // mardi et dimanche
    private $joursFeries;

    public function __construct(JoursFeries $joursFeries)
    {
        $this->joursFeries = $joursFeries;
    }

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

        $jours_feries = $this->joursFeries->joursFeries($value);
        if (in_array($value->format('d-m-Y'), $jours_feries))
        {
            $this->context
                ->buildViolation($constraint->messageJoursFeries)
                ->addViolation()
            ;
        }
    }
}