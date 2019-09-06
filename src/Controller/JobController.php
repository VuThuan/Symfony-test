<?php

namespace App\Controller;

use App\Entity\Job;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Category;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;

class JobController extends AbstractController
{
    /**
     * List all job entities
     * 
     * @Route("/", name="job.list", methods="GET")
     * 
     * @return Response
     */
    // public function list(EntityManagerInterface $em): Response
    // {
    //     $jobs = $em->getRepository(Job::class)->findActiveJobs();

    //     return $this->render('job/list.html.twig', [
    //         'jobs' => $jobs,
    //     ]);
    // }
    public function list(EntityManagerInterface $em): Response
    {
        $categories = $em->getRepository(Category::class)->findWithActiveJobs();

        return $this->render('job/list.html.twig', [
            'categories' => $categories,
        ]);
    }

    /**
     * Finds and displays a job entity.
     *
     * @Route("job/{id}", name="job.show", methods="GET", requirements={"id" = "\d+"})
     *
     * @Entity("job", expr="repository.findActiveJob(id)")
     *
     * @param Job $job
     *
     * @return Response
     */
    public function show(Job $job): Response
    {
        return $this->render('job/show.html.twig', [
            'job' => $job,
        ]);
    }
}
