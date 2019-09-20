<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AffiliateControllerTest extends WebTestCase
{
    public function testCompleteScenario()
    {
        // Create a new client to browse the application
        $client = static::createClient();

        // Create a new entry in the database
        $crawler = $client->request('GET', '/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /");

        $crawler = $client->click($crawler->selectLink('Affiliates')->link());

        $form = $crawler->selectButton('Create')->form(array(
            'affiliate[url]' => 'https://sun-asterisk.vn',
            'affiliate[email]' => 'vuthuan3090@gmail.com',
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();
    }
}
