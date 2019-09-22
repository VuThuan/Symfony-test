<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class JobControllerTest extends WebTestCase
{
    public function getMostRecentProgrammingJob()
    {
        $kernel = static::createKernel();
        $kernel->boot();
        $em = $kernel->getContainer()->get('doctrine.orm.entity_manager');

        $query = $em->createQuery('SELECT j FROM App:Job j LEFT JOIN j.category c WHERE c.slug = :slug AND j.expiresAt > :date ORDER BY j.createdAt DESC');
        $query->setParameter('slug', 'programming');
        $query->setParameter('date', date('Y-m-d H:i:s', time()));
        $query->setMaxResults(1);

        return $query->getSingleResult();
    }

    public function getExpiredJob()
    {
        $kernel = static::createKernel();
        $kernel->boot();
        $em = $kernel->getContainer()->get('doctrine.orm.entity_manager');

        $query = $em->createQuery('SELECT j FROM App:Job j WHERE j.expiresAt < :date');
        $query->setParameter('date', date('Y-m-d H:i:s', time()));
        $query->setMaxResults(1);

        return $query->getSingleResult();
    }

    public function testIndex()
    {
        $kernel = static::createKernel();
        $kernel->boot();
        $max_jobs_on_homepage = $kernel->getContainer()->getParameter('max_jobs_on_homepage');
        $max_jobs_on_category = $kernel->getContainer()->getParameter('max_jobs_on_category');

        $client = static::createClient();

        $crawler = $client->request('GET', '/');
        $this->assertEquals('App\Controller\JobController::list', $client->getRequest()->attributes->get('_controller'));

        //expired jobs are not listed
        $this->assertTrue($crawler->filter('.collapse p.navbar-text:contains("Expired")')->count() == 0);

        // only $max_jobs_on_homepage jobs are listed for a category
        // $this->assertTrue($crawler->filter('tbody tr')->count() <= $max_jobs_on_homepage);

        // jobs are sorted by date
        // $this->assertTrue($crawler->filter('tbody tr')->first()->filter(sprintf('a[href*="/job/%d"]', $this->getMostRecentProgrammingJob()->getId()))->count() == 1);

        // each job on the homepage is clickable and give detailed information
        $job = $this->getMostRecentProgrammingJob();
        $link = $crawler->selectLink('Web Developer')->first()->link();
        $crawler = $client->click($link);

        $this->assertEquals('App\Controller\JobController::show', $client->getRequest()->attributes->get('_controller'));

        $this->assertEquals($job->getCompany(), $client->getRequest()->attributes->get('job')->getCompany());
        $this->assertEquals($job->getLocation(), $client->getRequest()->attributes->get('job')->getLocation());
        $this->assertEquals($job->getPosition(), $client->getRequest()->attributes->get('job')->getPosition());
        $this->assertEquals($job->getId(), $client->getRequest()->attributes->get('job')->getId());

        //a non-existent job forwards the user to a 404
        $crawler = $client->request('GET', '/job/299');
        $this->assertTrue(404 == $client->getResponse()->getStatusCode());
    }

    public function testJobForm()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/job/create');
        $this->assertEquals('App\Controller\JobController::create', $client->getRequest()->attributes->get('_controller'));

        // $form = $crawler->selectButton('Create')->form(array(
        //     'job[type]'         => 'full-time',
        //     'job[company]'      => 'Sensio Labs',
        //     'job[logo]'         => 'abcd xyz.jpg',
        //     'job[url]'          => 'http://www.sensio.com/',
        //     'job[position]'     => 'Developer',
        //     'job[location]'     => 'Atlanta, USA',
        //     'job[description]'  => 'You will work with symfony to develop websites for our customers.',
        //     'job[howToApply]'   => 'Send me an email',
        //     'job[public]'       => 1,
        //     'job[activated]'    => 1,
        //     'job[email]'        => 'vuthuan3090@gmail.com',
        //     'job[category]'     => 7,
        // ));

        // $client->submit($form);
        // $this->assertEquals('App\Controller\JobController::create', $client->getRequest()->attributes->get('_controller'));
        // $client->followRedirect();
        // $this->assertEquals('App\Controller\JobController::preview', $client->getRequest()->attributes->get('_controller'));

        $kernel = static::createKernel();
        $kernel->boot();
        $em = $kernel->getContainer()->get('doctrine.orm.entity_manager');

        $query = $em->createQuery('SELECT count(j.id) FROM App:Job j WHERE j.location = :location AND j.activated IS NULL AND j.public = 0');
        $query->setParameter('location', 'Atlanta');

        $this->assertTrue(0 == $query->getSingleScalarResult());
    }

    public function createJob($values = array(), $publish = false)
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/job/create');
        $form = $crawler->selectButton('Create')->form(array_merge(array(
            'job[type]'         => 'full-time',
            'job[company]'      => 'Sensio Labs',
            'job[logo]'         => 'abcd xyz.jpg',
            'job[url]'          => 'http://www.sensio.com/',
            'job[position]'     => 'Developer',
            'job[location]'     => 'Atlanta, USA',
            'job[description]'  => 'You will work with symfony to develop websites for our customers.',
            'job[howToApply]'   => 'Send me an email',
            'job[public]'       => 1,
            'job[activated]'    => 1,
            'job[email]'        => 'vuthuan3090@gmail.com',
            'job[category]'     => 1,
        ), $values));
        $client->submit($form);
        $client->followRedirect();

        if ($publish) {
            $crawler = $client->getCrawler();
            $form = $crawler->selectButton('Publish')->form();
            $client->submit($form);
            $client->followRedirect();
        }
        return $client;
    }

    public function getJobByPosition($position)
    {
        $kernel = static::createKernel();
        $kernel->boot();
        $em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
        $query = $em->createQuery('SELECT j from App:Job j WHERE j.position = :position');
        $query->setParameter('position', $position);
        $query->setMaxResults(1);

        return $query->getSingleResult();
    }

    public function testPublishJob()
    {
        $client = $this->createJob(array('job[position]' => 'FOO1'));
        $crawler = $client->request('GET', '/job/32');
        $form = $crawler->selectButton('Publish')->form();

        $client->submit($form);
        $kernel = static::createKernel();
        $kernel->boot();
        $em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
        $query = $em->createQuery('SELECT count(j.id) from App:Job j WHERE j.position = :position AND j.activated = 1');
        $query->setParameter('position', 'FOO1');
        $this->assertTrue(0 < $query->getSingleScalarResult());
    }

    // public function testDeleteJob()
    // {
    //     $client = $this->createJob(array('job[position]' => 'FOO2'));
    //     $crawler = $client->request('GET', '/job/32');
    //     $form = $crawler->selectButton('Delete')->form();
    //     $client->submit($form);
    //     $kernel = static::createKernel();
    //     $kernel->boot();
    //     $em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
    //     $query = $em->createQuery('SELECT count(j.id) from AppBundle:Job j WHERE j.position = :position');
    //     $query->setParameter('position', 'FOO2');
    //     $this->assertTrue(0 == $query->getSingleScalarResult());
    // }

    public function testEditJob()
    {
        // $client = $this->createJob(array('job[position]' => 'FOO3'), true);
        $client = static::createClient();
        $crawler = $client->request('GET', sprintf('/job/%s/edit', $this->getJobByPosition('FOO3')->getToken()));
        $this->assertTrue(200 === $client->getResponse()->getStatusCode());
    }
}
