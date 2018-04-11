<?php

namespace Tests\AppBundle\Controller;


use AppBundle\Controller\LouvreController;
use AppBundle\Entity\Billet;
use AppBundle\Entity\Commande;
use AppBundle\Service\CalculerPrix;
use AppBundle\Service\EstDisponible;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LouvreControllerTest extends WebTestCase
{
    private $client;
    private $calculerPrix;
    private $billet;
    private $commande;

    const CATEGORIE_BEBE = array('age' => 4, 'prix' => 0, 'type' => 'Gratuit');
    const CATEGORIE_SENIOR = array('age' => 60, 'prix' => 12, 'type' => 'Senior');
    const CATEGORIE_REDUIT = array('prix' => 10, 'type' => 'Réduit');
    const CATEGORIE_ENFANT = array('age' => 12, 'prix' => 8, 'type' => 'Enfant');
    const CATEGORIE_NORMAL = array('prix' => 16, 'type' => "Plein Tarif");

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->client = static::createClient();
        $this->calculerPrix = new CalculerPrix();
        $this->billet = new Billet();
        $this->commande = new Commande();
    }

    public function testInfoFacturationAction()
    {
        $this->client->request('GET', '/louvre/info_facturation');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }


    public function testPrixBebe()
    {
        $date =  new \DateTime();
        $dateN = $date->setDate(2018, 03, 11);
        $dateN->format('dd-mm-Y');

        $dateBillet = new \DateTime('now');

        $commande = $this->commande
            ->setDateBillet($dateBillet);
        $billetTest = $this->billet
            ->setDateNaissance($date)
            ->setTarifReduit(false)
        ;
        $this->calculerPrix->prixBillet($billetTest, $commande);
        $prix = $billetTest->getPrix();
        $this->assertEquals(self::CATEGORIE_BEBE['prix'], $prix);
    }







}