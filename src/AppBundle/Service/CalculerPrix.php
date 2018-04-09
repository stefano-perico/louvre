<?php

namespace AppBundle\Service;


use AppBundle\Entity\Billet;
use AppBundle\Entity\Commande;


class CalculerPrix
{
    const CATEGORIE_BEBE = array('age' => 4, 'prix' => 0, 'type' => 'Gratuit');
    const CATEGORIE_SENIOR = array('age' => 60, 'prix' => 12, 'type' => 'Senior');
    const CATEGORIE_REDUIT = array('prix' => 10, 'type' => 'Réduit');
    const CATEGORIE_ENFANT = array('age' => 12, 'prix' => 8, 'type' => 'Enfant');
    const CATEGORIE_NORMAL = array('prix' => 16, 'type' => "Plein Tarif");
    const HEURE_LIMITE_JOURNEE = 14;


    public function prixBillet(Billet $billet, Commande $commande)
    {
        $dateBillet = $commande->getDateBillet();
        if($this->isDemiJournee($dateBillet))
        {
            $commande->setDemiJournee(true);
        }
        if ($commande->getDemiJournee() == true)
        {
            $prix = $this->setDemiJournee($billet);
            return $prix;
        }
        elseif ($billet->getTarifReduit() == true)
        {
            $prix = self::CATEGORIE_REDUIT['prix'];
            $billet
                ->setPrix(self::CATEGORIE_REDUIT['prix'])
                ->setType(self::CATEGORIE_REDUIT['type'])
            ;
            return $prix;
        }
        $prix = $this->setPrixJournee($billet);
        return $prix;
    }

    public function setDemiJournee(Billet $billet)
    {
        switch ($age = $billet->getAge())
        {
            case $age <= self::CATEGORIE_BEBE['age']:
                $prix = self::CATEGORIE_BEBE['prix'];
                $billet
                    ->setPrix(self::CATEGORIE_BEBE['prix'])
                    ->setType(self::CATEGORIE_BEBE['type'])
                ;
                break;
            case $age <= self::CATEGORIE_ENFANT['age']:
                $prix = self::CATEGORIE_ENFANT['prix']/2;
                $billet
                    ->setPrix(self::CATEGORIE_ENFANT['prix']/2)
                    ->setType(self::CATEGORIE_ENFANT['type'])
                ;
                break;
            case $age >= self::CATEGORIE_SENIOR['age']:
                $prix = self::CATEGORIE_SENIOR['prix']/2;
                $billet
                    ->setPrix(self::CATEGORIE_SENIOR['prix']/2)
                    ->setType(self::CATEGORIE_SENIOR['type'])
                ;
                break;
            default:
                $prix = self::CATEGORIE_NORMAL['prix']/2;
                $billet
                    ->setPrix(self::CATEGORIE_NORMAL['prix']/2)
                    ->setType(self::CATEGORIE_NORMAL['type'])
                ;
        }
        return $prix;
    }

    public function setPrixJournee(Billet $billet)
    {
        switch ($age = $billet->getAge())
        {
            case $age <= self::CATEGORIE_BEBE['age']:
                $prix = self::CATEGORIE_BEBE['prix'];
                $billet
                    ->setPrix(self::CATEGORIE_BEBE['prix'])
                    ->setType(self::CATEGORIE_BEBE['type'])
                ;
                break;
            case $age <= self::CATEGORIE_ENFANT['age']:
                $prix = self::CATEGORIE_ENFANT['prix'];
                $billet
                    ->setPrix(self::CATEGORIE_ENFANT['prix'])
                    ->setType(self::CATEGORIE_ENFANT['type'])
                ;
                break;
            case $age >= self::CATEGORIE_SENIOR['age']:
                $prix = self::CATEGORIE_SENIOR['prix'];
                $billet
                    ->setPrix(self::CATEGORIE_SENIOR['prix'])
                    ->setType(self::CATEGORIE_SENIOR['type'])
                ;
                break;
            default:
                $prix = self::CATEGORIE_NORMAL['prix'];
                $billet
                    ->setPrix(self::CATEGORIE_NORMAL['prix'])
                    ->setType(self::CATEGORIE_NORMAL['type'])
                ;
        }
        return $prix;
    }

    private function isDemiJournee(\DateTime $dateBillet)
    {
        $dateDuJour = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
        $heureLimite = new \DateTime();
        $heureLimite->setTime(self::HEURE_LIMITE_JOURNEE,0);
        if($dateBillet->format('d-m-Y') == $dateDuJour->format('d-m-Y') ) {
            if ($dateDuJour->format('h:i' > $heureLimite->format('h:i'))) {
                return true;
            }
        }
        return false;
    }
}