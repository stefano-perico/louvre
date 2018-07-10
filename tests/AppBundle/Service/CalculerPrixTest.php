<?php

namespace Tests\AppBundle\Service;

use AppBundle\Entity\Billet;
use AppBundle\Entity\Commande;
use AppBundle\Service\CalculerPrix;
use PHPUnit\Framework\TestCase;

class CalculerPrixTest extends TestCase
{
    const DATE_VISITE = '2018-08-02';

    protected $bebe = array('age' => '2014-05-02', 'prix' => 0, 'type' => 'Gratuit'); // <= 4
    protected $senior = array('age' => '1957-05-02', 'prix' => 12, 'type' => 'Senior'); // >= 60
    protected $reduit = array('prix' => 10, 'type' => 'Réduit');
    protected $enfant = array('age' => '2006-05-02', 'prix' => 8, 'type' => 'Enfant'); // <= 12
    protected $normal = array('age' => '1985-02-07', 'prix' => 16, 'type' => "Plein Tarif");

    /**
     * @dataProvider prixPourBillets
     */
    public function testPrixBillets($age, $prix)
    {
        $dateVisite = new \DateTime(self::DATE_VISITE);
        $commande = new Commande();
        $commande->setDateBillet($dateVisite);

        $billet =  new Billet();
        $dateNaissance = new \DateTime($age);
        $billet->setDateNaissance($dateNaissance);

        $prixBillet = new CalculerPrix();
        $test = $prixBillet->prixBillet($billet, $commande);

        $this->assertSame($prix, $test);
    }

    public function prixPourBillets()
    {
        return [
            [$this->bebe['age'], 0],     // Tarif bébé
            [$this->senior['age'], 12],   // Tarif sénior
            [$this->enfant['age'], 8],    // Tarif Enfant
            [$this->normal['age'], 16]    // Tarif Normal
        ];
    }

    /**
     * @dataProvider prixPourBilletsReduit
     */
    public function testPrixBilletsReduit($age, $prix)
    {
        $dateVisite = new \DateTime(self::DATE_VISITE);
        $commande = new Commande();
        $commande->setDateBillet($dateVisite);

        $billet =  new Billet();
        $dateNaissance = new \DateTime($age);
        $billet->setDateNaissance($dateNaissance);
        $billet->setTarifReduit(true);

        $prixBillet = new CalculerPrix();
        $test = $prixBillet->prixBillet($billet, $commande);

        $this->assertSame($prix, $test);
    }

    public function prixPourBilletsReduit()
    {
        return [
            [$this->senior['age'], 10],   // Tarif sénior
            [$this->enfant['age'], 10],    // Tarif Enfant
            [$this->normal['age'], 10]    // Tarif Normal
        ];
    }

    /**
     * @dataProvider prixPourBilletsDemi
     */
    public function testPrixBilletsDemi($age, $prix)
    {
        $dateVisite = new \DateTime(self::DATE_VISITE);
        $commande = new Commande();
        $commande->setDateBillet($dateVisite);
        $commande->setDemiJournee(true);

        $billet =  new Billet();
        $dateNaissance = new \DateTime($age);
        $billet->setDateNaissance($dateNaissance);

        $prixBillet = new CalculerPrix();
        $test = $prixBillet->prixBillet($billet, $commande);

        $this->assertSame($prix, $test);
    }

    public function prixPourBilletsDemi()
    {
        return [
            [$this->bebe['age'], 0],      // Tarif bébé
            [$this->senior['age'], 6],    // Tarif sénior
            [$this->enfant['age'], 4],    // Tarif Enfant
            [$this->normal['age'], 8]     // Tarif Normal
        ];
    }

}