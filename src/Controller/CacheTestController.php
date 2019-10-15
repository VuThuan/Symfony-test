<?php

namespace App\Controller;

use App\Service\JobHistoryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\EventListener\AbstractSessionListener;

class CacheTestController extends AbstractController
{

    function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * @param Request $request
     * 
     * @return Response
     * @throws \Exception
     */
    public function index(Request $request)
    {
        $response = new Response();
        $response->headers->set(AbstractSessionListener::NO_AUTO_CACHE_CONTROL_HEADER, 'true');

        $response->setEtag(md5($this->generateRandomString(15)));
        $response->setLastModified(new \DateTime('yesterday'));
        $response->setPublic();

        $response->setSharedMaxAge(600);

        $response->headers->addCacheControlDirective('must-revalidate', true);

        if ($response->isNotModified($request)) {
            return $response;
        }

        return $response;
    }

    /**
     * @param $jobHistory History job has watch
     * 
     * @return Response
     */
    public function historyAction(JobHistoryService $jobHistoryService)
    {
        $response =  $this->render('job/_job_history.html.twig', [
            'historyJobs' => $jobHistoryService->getJobs()
        ]);

        $response->setSharedMaxAge(10);

        return $response;
    }
}
