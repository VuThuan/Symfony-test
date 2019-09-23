<?php

namespace App\Controller\API;

use App\Entity\Affiliate;
use App\Entity\Job;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Response;

class JobController extends FOSRestController
{
    //, requirements={"token" = "\w\d+"}    
    /**
     * @Rest\Get("/{token}/jobs", name="api.job.list", requirements={"token" = "\w\d+"} )
     *
     * @param Affiliate $affiliate
     * @param EntityManagerInterface $em
     *
     * @return Response
     */
    public function getJobsAction(Affiliate $affiliate, EntityManagerInterface $em): Response
    {
        $jobs = $em->getRepository(Job::class)->findActiveJobs();

        return $this->handleView($this->view($jobs, Response::HTTP_OK));
    }
}
