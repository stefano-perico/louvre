<?php
/**
 * Created by PhpStorm.
 * User: stefano
 * Date: 15/07/18
 * Time: 18:42
 */

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class OrderSummaryControllerTest extends WebTestCase
{
    public function testOrderSummaryAction()
    {
        $client = static::createClient();
        $client->request('GET','louvre/recap');

        $this->assertSame(500, $client->getResponse()->getStatusCode());
    }
}