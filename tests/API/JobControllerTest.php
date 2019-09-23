<?php

namespace App\Tests\API;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class JobControllerTest extends WebTestCase
{
    public function testGetJobs()
    {
        $client = static::createClient();

        // A token is needed to access the service
        $crawler = $client->request('GET', '/api/jobs.json');
        $this->assertTrue(404 === $client->getResponse()->getStatusCode());

        // An inactive account cannot access the web service
        $crawler = $client->request('GET', '/api/jobs/symfony.json');
        $this->assertTrue(404 === $client->getResponse()->getStatusCode());

        // The web service supports the JSON format ?????????
        $crawler = $client->request('GET', '/api/v1/sensio_labs/jobs');
        $this->assertTrue(200 === $client->getResponse()->getStatusCode());
    }
}
