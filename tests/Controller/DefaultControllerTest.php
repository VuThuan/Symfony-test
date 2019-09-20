<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function provideUrls()
    {
        return [
            ['/'],
            ['job/create'],
            ['job/1'],
            ['/affiliate/create'],
            ['/job/job_100'],
        ];
    }

    /**
     * A basic function test status of path
     * 
     * @dataProvider provideUrls
     */
    public function testRoute($url)
    {
        $client = static::createClient();

        $crawler = $client->request('GET', $url);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertSame(1, $crawler->filter('a:contains("Jobeet")')->count());
    }
}
