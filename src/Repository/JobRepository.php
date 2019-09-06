<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use App\Entity\Job;

class JobRepository extends EntityRepository
{
    /**
     * @param int $id
     *
     * @return Job|null
     */
    public function findActiveJob(int $id): ?Job
    {
        return $this->createQueryBuilder('j')
            ->where('j.id = :id')
            ->andWhere('j.expiresAt > :date')
            ->setParameter('id', $id)
            ->setParameter('date', new \DateTime())
            ->getQuery()
            ->getOneOrNullResult();
    }
}
