<?php

namespace Tests\AppBundle\Controller;



use AppBundle\Entity\Billet;
use AppBundle\Entity\Commande;
use AppBundle\Service\CalculerPrix;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LouvreControllerTest extends WebTestCase
{

    public function testAccueilActionIsUp()
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }

    public function testAccueilAction()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertSame(1, $crawler->filter('html:contains("Bienvenue au Louvre")')->count());
    }

    public function testInfoFacturationActionIsUp()
    {
        $client = static::createClient();
        $client->request('GET', 'louvre/info_facturation');

        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }

    public function testInfoFacturationAction()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $link = $crawler->selectLink('Billetterie')->link();
        $crawler = $client->click($link);

        $form = $crawler->selectButton('Valider')->form();
        $form['appbundle_utilisateurs[nom]'] = 'John';
        $form['appbundle_utilisateurs[prenom]'] = 'Doe';
        $form['appbundle_utilisateurs[adresse]'] = '36 rue de la rue';
        $form['appbundle_utilisateurs[ville]'] = 'ville';
        $form['appbundle_utilisateurs[codePostal]'] = '92000';
        $form['appbundle_utilisateurs[pays]'] = 'France';
        $form['appbundle_utilisateurs[email]'] = 'email@email.fr';
        $form['appbundle_utilisateurs[telephone]'] = '01 70 01 01 01';
        $client->submit($form);

        $crawler = $client->followRedirect();

        $this->assertSame(1, $crawler->filter('aside.alert.alert-success')->count());
    }

    public function testPanierActionIsUp()
    {
        $client = static::createClient();
        $client->request('GET', 'louvre/panier');

        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }

    public function testPanierAction()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', 'louvre/panier');

        $form = $crawler->selectButton('Valider')->form();
        $form->setValues([
            'appbundle_commandes[dateBillet]'                       =>  '30-06-2018',
            'appbundle_commandes[billets][0][nom]'                  =>  'Doe',
            'appbundle_commandes[billets][0][prenom]'               => 'John',
            'appbundle_commandes[billets][0][dateNaissance][month]' => '1',
            'appbundle_commandes[billets][0][dateNaissance][day]'   => '1',
            'appbundle_commandes[billets][0][dateNaissance][year]'  => '1998'
        ]);

        $client->submit($form);

        $client->followRedirect();

        echo $client->getResponse()->getContent();
    }

    public function testRecapActionIsUp()
    {
        $client = static::createClient();
        $client->request('GET','louvre/recap');

         $this->assertSame(200, $client->getResponse()->getStatusCode());
    }

    public function testValidationActionIsUp()
    {
        $client = static::createClient();
        $client->request('GET', 'louvre/validation');

        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }









}