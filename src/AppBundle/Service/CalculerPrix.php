<?php

namespace AppBundle\Service;


use AppBundle\Entity\Billet;
use AppBundle\Entity\Commande;


class CalculerPrix
{
    const HEURE_LIMITE_JOURNEE = 14;

    protected $bebe = array('age' => 4, 'prix' => 0, 'type' => 'Gratuit');
    protected $senior = array('age' => 60, 'prix' => 12, 'type' => 'Senior');
    protected $reduit = array('prix' => 10, 'type' => 'Réduit');
    protected $enfant = array('age' => 12, 'prix' => 8, 'type' => 'Enfant');
    protected $normal = array('prix' => 16, 'type' => "Plein Tarif");
    

    public function prixBillet(Billet $billet, Commande $commande)
    {
        $dateBillet = $commande->getDateBillet();
        if($this->isDemiJournee($dateBillet))
        {
            $commande->setDemiJournee(true);
        }
        if ($billet->getTarifReduit() == true)
        {
            if ($commande->getDemiJournee() == true)
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
        elseif ($commande->getDemiJournee() == true)
        {
            $prix = $this->setDemiJournee($billet);
            return $prix;
        }
        $prix = $this->setPrixJournee($billet);
        return $prix;
    }

    public function setDemiJournee(Billet $billet)
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

    public function setPrixJournee(Billet $billet)
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