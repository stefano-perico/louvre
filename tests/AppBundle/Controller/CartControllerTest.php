<?php

namespace Tests\AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CartControllerTest extends WebTestCase
{

    public function testCartActionIsUp()
    {
        $client = static::createClient();
        $client->request('GET', 'louvre/panier');

        $this->assertSame(200, $client->getResponse()->getStatusCode());
        echo $client->getResponse()->getContent();
    }

    public function testValidationActionIsUp()
    {
        $client = static::createClient();
        $client->request('GET', 'louvre/validation');

        $this->assertSame(200, $client->getResponse()->getStatusCode());
        echo $client->getResponse()->getContent();
    }









}