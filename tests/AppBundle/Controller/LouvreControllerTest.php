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
        $this->client->request('GET', 'louvre/info_facturation');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testAccueilAction()
    {
        $this->client->request('GET', '');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testPanierAction()
    {
        $this->client->request('GET', 'louvre/panier/utilisateur:1');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testRecapAction()
    {
        $this->client->request('GET', 'louvre/recap/commande:2/utilisateur:2');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testValidationAction()
    {
        $this->client->request('GET', 'louvre/validation');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }









}