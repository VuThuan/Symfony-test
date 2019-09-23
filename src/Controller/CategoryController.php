<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Job;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Service\JobHistoryService;
use Symfony\Component\Cache\Adapter\AdapterInterface;

class CategoryController extends Controller
{
    /**
     * Finds and displays a category entity.
     *
     * @Route(
     *     "/category/{slug}/{page}",
     *     name="category.show",
     *     methods="GET",
     *     defaults={"page": 1},
     *     requirements={"page" = "\d+"}
     * )
     *
     * @param Category $category
     * @param PaginatorInterface $paginator
     * @param int $page
     * @param JobHistoryService $jobHistoryService
     * @param AdapterInterface $cache
     *
     * @return Response
     */
    public function show(
        Category $category,
        PaginatorInterface $paginator,
        int $page,
        JobHistoryService $jobHistoryService,
        AdapterInterface $cache
    ): Response {

        $item = $cache->getItem('activeJob');

        if (!$item->isHit()) {
            $item->set($paginator->paginate(
                $this->getDoctrine()->getRepository(Job::class)->getPaginatedActiveJobsByCategoryQuery($category),
                $page,
                $this->getParameter('max_jobs_on_category')
            ));
            $cache->save($item);
        }

        $activeJobs = $item->get('activeJob');

        return $this->render('category/show.html.twig', [
            'category' => $category,
            'activeJobs' => $activeJobs,
            'historyJobs' => $jobHistoryService->getJobs(),
        ]);
    }
}
