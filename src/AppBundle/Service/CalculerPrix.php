<?php

namespace AppBundle\Service;

use AppBundle\Entity\Billet;
use AppBundle\Entity\Commande;

class CalculerPrix
{
    private $bebe = array('age' => 4, 'prix' => 0, 'type' => 'Gratuit');
    private $senior = array('age' => 60, 'prix' => 12, 'type' => 'Senior');
    private $reduit = array('prix' => 10, 'type' => 'Réduit');
    private $enfant = array('age' => 12, 'prix' => 8, 'type' => 'Enfant');
    private $normal = array('prix' => 16, 'type' => "Plein Tarif");
    

    public function prixBillet(Billet $billet, Commande $commande)
    {
        if ($billet->getTarifReduit())
        {
            if ($commande->getDemiJournee())
            {
                $prix = $this->reduit['prix']/2;
                $billet
                    ->setPrix($this->reduit['prix']/2)
                    ->setType($this->reduit['type']. ', Demi journée')
                ;
                return $prix;
            }
            $prix = $this->reduit['prix'];
            $billet
                ->setPrix($this->reduit['prix'])
                ->setType($this->reduit['type'])
            ;
            return $prix;
        }
        elseif ($commande->getDemiJournee())
        {
            $prix = $this->setDemiJournee($billet);
            return $prix;
        }
        $prix = $this->setPrixJournee($billet);
        return $prix;
    }

    private function setDemiJournee(Billet $billet)
    {
        switch ($age = $billet->getAge())
        {
            case $age >= $this->senior['age']:
                $prix = $this->senior['prix']/2;
                $billet
                    ->setPrix($this->senior['prix']/2)
                    ->setType($this->senior['type']. ', Demi journée')
                ;
                break;
            case $age <= $this->bebe['age']:
                $prix = $this->bebe['prix'];
                $billet
                    ->setPrix($this->bebe['prix'])
                    ->setType($this->bebe['type']. ', Demi journée')
                ;
                break;
            case $age <= $this->enfant['age']:
                $prix = $this->enfant['prix']/2;
                $billet
                    ->setPrix($this->enfant['prix']/2)
                    ->setType($this->enfant['type']. ', Demi journée')
                ;
                break;
            default:
                $prix = $this->normal['prix']/2;
                $billet
                    ->setPrix($this->normal['prix']/2)
                    ->setType($this->normal['type']. ', Demi journée')
                ;
        }
        return $prix;
    }

    private function setPrixJournee(Billet $billet)
    {
        switch ($age = $billet->getAge())
        {
            case $age >= $this->senior['age']:
                $prix = $this->senior['prix'];
                $billet
                    ->setPrix($this->senior['prix'])
                    ->setType($this->senior['type'])
                ;
                break;
            case $age <= $this->bebe['age']:
                $prix = $this->bebe['prix'];
                $billet
                    ->setPrix($this->bebe['prix'])
                    ->setType($this->bebe['type'])
                ;
                break;
            case $age <= $this->enfant['age']:
                $prix = $this->enfant['prix'];
                $billet
                    ->setPrix($this->enfant['prix'])
                    ->setType($this->enfant['type'])
                ;
                break;
            default:
                $prix = $this->normal['prix'];
                $billet
                    ->setPrix($this->normal['prix'])
                    ->setType($this->normal['type'])
                ;
        }
        return $prix;
    }
}