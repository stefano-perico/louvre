<?php


namespace AppBundle\Validator\Constraints;


use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class JoursFermee extends Constraint
{
    public $message = "Vous ne pouvez pas réserver de billet pour cette date";

}