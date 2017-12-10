<?php
/**
 * Created by PhpStorm.
 * User: stefa
 * Date: 10/12/2017
 * Time: 16:31
 */

namespace AppBundle\Service;


use AppBundle\Entity\Billet;

class CalculerPrix
{
    private $normal = 16;
    private $enfant = 8;
    private $senior = 12;
    private $reduit = 10;
    private $gratuit = 0;


    public function calculerPrix(Billet $billet)
    {
        $age = $billet->getAge();
        if($age < 4)
        {
            return $this->gratuit;
        }
        elseif ($age >= 4 && $age <= 12)
        {
            return $this->enfant;
        }
        elseif ($age >= 60)
        {
            return $this->senior;
        }
        else
        {
            return $this->normal;
        }
    }
}