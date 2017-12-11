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
    const CATEGORIE_BEBE = array('age' => 4, 'prix' => 0, 'type' => 'Gratuit');
    const CATEGORIE_SENIOR = array('age' => 60, 'prix' => 12, 'type' => 'Senior');
    const CATEGORIE_REDUIT = array('prix' => 10, 'type' => 'Réduit');
    const CATEGORIE_ENFANT = array('age' => 12, 'prix' => 8, 'type' => 'Enfant');
    const CATEGORIE_NORMAL = array('prix' => 16, 'type' => "Plein Tarif");


    public function prixBillet(Billet $billet)
    {
        if ($billet->getTarifReduit() == true)
        {
            $billet
                ->setPrix(self::CATEGORIE_REDUIT['prix'])
                ->setType(self::CATEGORIE_REDUIT['type'])
            ;
        }
        $age = $billet->getAge();
        switch ($age)
        {
            case $age <= self::CATEGORIE_BEBE['age']:
                $billet
                    ->setPrix(self::CATEGORIE_BEBE['prix'])
                    ->setType(self::CATEGORIE_BEBE['type'])
                ;
                break;
            case $age <= self::CATEGORIE_ENFANT['age']:
                $billet
                    ->setPrix(self::CATEGORIE_ENFANT['prix'])
                    ->setType(self::CATEGORIE_ENFANT['type'])
                ;
                break;
            case $age >= self::CATEGORIE_SENIOR['age']:
                $billet
                    ->setPrix(self::CATEGORIE_SENIOR['prix'])
                    ->setType(self::CATEGORIE_SENIOR['type'])
                ;
                break;
            default:
                $billet
                    ->setPrix(self::CATEGORIE_NORMAL['prix'])
                    ->setType(self::CATEGORIE_NORMAL['type'])
                ;
        }
    }
}