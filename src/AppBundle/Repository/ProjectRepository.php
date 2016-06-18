<?php

namespace AppBundle\Repository;

use AppBundle\Entity\George;
use AppBundle\Entity\Project;
use LAG\AdminBundle\Repository\DoctrineRepository;

/**
 * ProjectRepository
 */
class ProjectRepository extends DoctrineRepository
{
    /**
     * @param George $george
     * @return Project[]
     */
    public function findForGeorge(George $george)
    {
        return $this
            ->createQueryBuilder('project')
            ->addSelect('profiles, george, workedDays')
            ->innerJoin('project.profiles', 'profiles')
            ->innerJoin('profiles.george', 'george')
            ->innerJoin('profiles.workedDays', 'workedDays')
            ->where('george.id = :george_id')
            ->setParameter('george_id', $george->getId())
            ->getQuery()
            ->getResult();
    }
}
