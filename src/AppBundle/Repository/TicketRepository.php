<?php

namespace AppBundle\Repository;

/**
 * TicketRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TicketRepository extends \Doctrine\ORM\EntityRepository
{
    public function findAllMoviesByMovieIdAndCinemaId($movieId, $cinemaId)
    {
        $query = $this->getEntityManager()
            ->createQuery(
                'SELECT cm FROM AppBundle:Cinema_hall_has_Movie cm WHERE cm.movieId = :movieId AND cm.cinemaHallId IN (
                        SELECT ch.id FROM AppBundle:Cinema_hall ch WHERE ch.cinemaId = :cinemaId
                    )'
            )->setParameters(['movieId'=>$movieId, 'cinemaId'=>$cinemaId]);
        return $query->getResult();
    }

}
