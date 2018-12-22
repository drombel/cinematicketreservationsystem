<?php

namespace AppBundle\Repository;

/**
 * CinemaRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CinemaRepository extends \Doctrine\ORM\EntityRepository
{
    public function findAllWithCity()
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.city', 'c')
            ->addSelect('c')
            ->getQuery()->getResult();
    }
}
