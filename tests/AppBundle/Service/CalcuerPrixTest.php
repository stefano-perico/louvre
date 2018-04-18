<?php

namespace Tests\AppBundle\Service;

use AppBundle\Entity\Billet;
use AppBundle\Entity\Commande;
use AppBundle\Service\CalculerPrix;
use PHPUnit\Framework\TestCase;

class CalcuerPrixTest extends TestCase
{
    const DATE_VISITE = '2018-06-02';
    const CATEGORIE_BEBE = array('age' => '2014-05-02', 'prix' => 0, 'type' => 'Gratuit'); // <= 4
    const CATEGORIE_SENIOR = array('age' => '1957-05-02', 'prix' => 12, 'type' => 'Senior'); // >= 60
    const CATEGORIE_ENFANT = array('age' => '2006-05-02', 'prix' => 8, 'type' => 'Enfant'); // <= 12
    const CATEGORIE_REDUIT = array('prix' => 10, 'type' => 'Réduit');
    const CATEGORIE_NORMAL = array('age' => '1985-02-07', 'prix' => 16, 'type' => "Plein Tarif");


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
            [self::CATEGORIE_BEBE['age'], 0],     // Tarif bébé
            [self::CATEGORIE_SENIOR['age'], 12],   // Tarif sénior
            [self::CATEGORIE_ENFANT['age'], 8],    // Tarif Enfant
            [self::CATEGORIE_NORMAL['age'], 16]    // Tarif Normal
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
            [self::CATEGORIE_SENIOR['age'], 10],   // Tarif sénior
            [self::CATEGORIE_ENFANT['age'], 10],    // Tarif Enfant
            [self::CATEGORIE_NORMAL['age'], 10]    // Tarif Normal
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
            [self::CATEGORIE_BEBE['age'], 0],     // Tarif bébé
            [self::CATEGORIE_SENIOR['age'], 6],    // Tarif sénior
            [self::CATEGORIE_ENFANT['age'], 4],    // Tarif Enfant
            [self::CATEGORIE_NORMAL['age'], 8]     // Tarif Normal
        ];
    }

}