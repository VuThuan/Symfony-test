<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class JobControllerTest extends WebTestCase
{
    public function testCompleteScenario()
    {
        // Create a new client to browse the application
        $client = static::createClient();

        // Create a new entry in the database
        $crawler = $client->request('GET', '/job/5');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /job/");

        $crawler = $client->click($crawler->selectLink('Post a Job')->link());

        //FIll in the form and submit it
        $form = $crawler->selectButton('Create')->form(array(
            'job[type]' => 'freelance',
            'job[company]' => 'opentechiz',
            'job[logo]' => 'admin.jpg',
            'job[url]' => 'onlinebiz.com.vn',
            'job[position]' => 'Developer',
            'job[location]' => 'Ha Noi',
            'job[description]' => 'easy',
            'job[howToApply]' => 'money',
            'job[public]' => 1,
            'job[activated]' => 1,
            'job[email]' => 'vuthuan3090@gmail.com',
            'job[category]' => 1
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        //Check data in the show view
        $this->assertSame(0, $crawler->filter('td:contains("opentechiz")')->count(), 'Missing element td:contains("opentechiz")');
    }

    public function testEditJob()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/job/job_102/edit');

        //Edit the entity
        $form = $crawler->selectButton('Edit')->form(array(
            'job[company]' => 'bussiness'
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check the element contains an attribute with value equals "bussiness"
        $this->assertSame(0, $crawler->filter('[value="bussiness"]')->count(), 'Missing element [value="bussiness"]');
    }

    // public function testDeleteJob()
    // {
    //     $client = static::createClient();

    //     $crawler = $client->request('GET', 'job/4');

    //     //Delete the entity
    //     $client->submit($crawler->selectButton('Delete')->form());
    //     $crawler = $client->followRedirect();

    //     // Check the entity has been delete on the list
    //     $this->assertNotRegExp('/Company 103/', $client->getResponse()->getContent());
    // }
}
