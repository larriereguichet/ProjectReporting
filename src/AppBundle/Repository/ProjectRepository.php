<?php

namespace AppBundle\Repository;

use AppBundle\Entity\George;
use AppBundle\Entity\Project;
use Doctrine\ORM\EntityRepository;

/**
 * ProjectRepository
 */
class ProjectRepository extends EntityRepository
{
    /**
     * @param George $george
     * @return Project[]
     */
    public function findForGeorge($george)
    {
        return $this
            ->createQueryBuilder('project')
            ->addSelect('profiles, george, workedDays')
            ->innerJoin('project.profiles', 'profiles')
            ->innerJoin('profiles.george', 'george')
            ->innerJoin('profiles.workedDays', 'workedDays')
            ->where('george.id = :george_id')
            ->setParameter('george_id', 1)
            ->getQuery()
            ->getResult();
    }
}
