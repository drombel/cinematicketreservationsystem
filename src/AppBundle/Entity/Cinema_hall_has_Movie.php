<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cinema_hall_has_Movie
 *
 * @ORM\Table(name="cinema_hall_has__movie")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Cinema_hall_has_MovieRepository")
 */
class Cinema_hall_has_Movie
{
    /**
     * @var int
     *
     * @ORM\Column(name="movieId", type="integer")
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Movie")
     */
    private $movieId;

    /**
     * @var int
     *
     * @ORM\Column(name="cinema_hallId", type="integer")
     * @ORM\ManyToOne(targetEntity="Cinema_hall")
     * @ORM\Id
     */
    private $cinemaHallId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="time", type="datetime")
     */
    private $time;

    /**
     * Set movieId
     *
     * @param integer $movieId
     *
     * @return Cinema_hall_has_Movie
     */
    public function setMovieId($movieId)
    {
        $this->movieId = $movieId;

        return $this;
    }

    /**
     * Get movieId
     *
     * @return int
     */
    public function getMovieId()
    {
        return $this->movieId;
    }

    /**
     * Set cinemaHallId
     *
     * @param integer $cinemaHallId
     *
     * @return Cinema_hall_has_Movie
     */
    public function setCinemaHallId($cinemaHallId)
    {
        $this->cinemaHallId = $cinemaHallId;

        return $this;
    }

    /**
     * Get cinemaHallId
     *
     * @return int
     */
    public function getCinemaHallId()
    {
        return $this->cinemaHallId;
    }

    /**
     * Set time
     *
     * @param \DateTime $time
     *
     * @return Cinema_hall_has_Movie
     */
    public function setTime($time)
    {
        $this->time = $time;

        return $this;
    }

    /**
     * Get time
     *
     * @return \DateTime
     */
    public function getTime()
    {
        return $this->time;
    }
}

