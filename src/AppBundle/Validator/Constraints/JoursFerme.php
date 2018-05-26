<?php


namespace AppBundle\Validator\Constraints;


use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class JoursFerme extends Constraint
{
    public $messageJoursFermes = "Vous ne pouvez pas réserver de billet les mardi et dimanche";
    public $messageJoursFeries = "Vous ne pouvez pas réserver de billet pour cette date";
}