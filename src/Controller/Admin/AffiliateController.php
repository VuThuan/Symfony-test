<?php

namespace App\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Affiliate;

class AffiliateController extends AbstractController
{
    /**
     * List all affiliate entities
     * 
     * @Route("/admin/affiliate/{page}", name="admin.affiliate.list", methods="GET", defaults={"page": 1}, requirements={"page" = "\d+"})
     * 
     * @param EntityManagerInterface $em
     * @param PaginatorInterface $paginator
     * @param int $page
     * 
     * @return Response
     */
    public function list(EntityManagerInterface $em, PaginatorInterface $paginator, int $page): Response
    {
        $affiliates = $paginator->paginate(
            $em->getRepository(Affiliate::class)->createQueryBuilder('a'),
            $page,
            $this->getParameter('max_per_page'),
            [
                PaginatorInterface::DEFAULT_SORT_FIELD_NAME => 'a.active',
                PaginatorInterface::DEFAULT_SORT_DIRECTION => 'ASC',
            ]
        );

        return $this->render('admin/affiliate/list.html.twig', [
            'affiliates' => $affiliates,
        ]);
    }

    /**
     * Activate affiliate
     * 
     * @Route("/admin/affiliate/{id}/activate",
     *      name="admin.affiliate.activate",
     *      methods="GET",
     *      requirements={"id" = "\d+"}     
     * )
     * 
     * @param EntityManagerInterface $em
     * @param Affiliate $affiliate
     * 
     * @return Response
     */
    public function activate(EntityManagerInterface $em, Affiliate $affiliate): Response
    {
        $affiliate->setActive(true);

        $em->flush();

        return $this->redirectToRoute('admin.affiliate.list');
    }

    /**
     * Deactivate affiliate.
     *
     * @Route("/admin/affiliate/{id}/deactivate",
     *     name="admin.affiliate.deactivate",
     *     methods="GET",
     *     requirements={"id" = "\d+"}
     * )
     *
     * @param EntityManagerInterface $em
     * @param Affiliate $affiliate
     *
     * @return Response
     */
    public function deactivate(EntityManagerInterface $em, Affiliate $affiliate): Response
    {
        $affiliate->setActive(false);
        $em->flush();

        return $this->redirectToRoute('admin.affiliate.list');
    }
}
