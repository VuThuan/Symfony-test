<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AffiliateControllerTest extends WebTestCase
{
    public function getProgrammingCategory()
    {
        $kernel = static::createKernel();
        $kernel->boot();
        $em = $kernel->getContainer()->get('doctrine.orm.entity_manager');

        $query = $em->createQuery('SELECT c FROM App:Category c WHERE c.slug = :slug');
        $query->setParameter('slug', 'programming');
        $query->setMaxResults(1);

        return $query->getSingleResult();
    }

    public function testAffiliateForm()
    {
        $client = static::createClient();

        //An affiliate can create account 
        $crawler = $client->request('GET', '/affiliate/create');
        $this->assertEquals('App\Controller\AffiliateController::create', $client->getRequest()->attributes->get('_controller'));

        $form = $crawler->selectButton('Create')->form(array(
            'affiliate[url]' => 'https://www.sun-asterisk.com',
            'affiliate[email]' => 'vuthuan3090@gmail.com',
            'affiliate[categories][1]' => $this->getProgrammingCategory()->getId()
        ));

        $crawler = $client->submit($form);
        $this->assertEquals('App\Controller\AffiliateController::create', $client->getRequest()->attributes->get('_controller'));
        $crawler = $client->followRedirect();
        $this->assertEquals('App\Controller\AffiliateController::wait', $client->getRequest()->attributes->get('_controller'));

        $this->assertTrue($crawler->filter('h3.text-center')->count() == 1);
        $this->assertTrue($crawler->filter('h3.text-center b')->text() == 'Your affiliate account has been created');
    }
}
