<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CategoryControllerTest extends WebTestCase
{
    public function testShow()
    {
        $kernel = static::createKernel();
        $kernel->boot();
        $max_jobs_on_homepage = $kernel->getContainer()->getParameter('max_jobs_on_homepage');
        $max_jobs_on_category = $kernel->getContainer()->getParameter('max_jobs_on_category');

        //Category on homepage are clickable
        $client = static::createClient();
        $crawler = $client->request('GET', '/category/programming');
        $this->assertEquals('App\Controller\CategoryController::show', $client->getRequest()->attributes->get('_controller'));
        $this->assertTrue(200 === $client->getResponse()->getStatusCode());

        //categories with more than $max_jobs_on_homepage jobs also have a "more" link
        $crawler = $client->request('GET', '/');
        $link = $crawler->selectLink('Programming')->link();
        $crawler = $client->click($link);

        $this->assertEquals('App\Controller\CategoryController::show', $client->getRequest()->attributes->get('_controller'));
        $this->assertEquals('programming', $client->getRequest()->attributes->get('slug'));

        //only $max_jobs_on_category jobs are listed
        $this->assertTrue($crawler->filter('tbody tr')->count() <= $max_jobs_on_category);
        $this->assertRegExp('/Previous/', $crawler->filter('.pagination')->text());
        $this->assertRegExp('/Next/', $crawler->filter('.pagination')->text());

        $link = $crawler->selectLink('2')->link();
        $crawler = $client->click($link);
        $this->assertEquals(2, $client->getRequest()->attributes->get('page'));
    }
}
