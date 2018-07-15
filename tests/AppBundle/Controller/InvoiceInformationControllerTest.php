<?php
/**
 * Created by PhpStorm.
 * User: stefano
 * Date: 15/07/18
 * Time: 18:41
 */

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class InvoiceInformationControllerTest extends WebTestCase
{
    public function testInvoiceInformationActionIsUp()
    {
        $client = static::createClient();
        $client->request('GET', 'louvre/info_facturation');

        $this->assertSame(200, $client->getResponse()->getStatusCode());
        echo $client->getResponse()->getContent();
    }

    public function testInvoiceInformationAction()
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
        echo $client->getResponse()->getContent();
    }
}