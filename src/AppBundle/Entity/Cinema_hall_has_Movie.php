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
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="movieId", type="integer")
     * @ORM\ManyToOne(targetEntity="Movie")
     */
    private $movieId;

    /**
     * @var int
     *
     * @ORM\Column(name="cinema_hallId", type="integer")
     * @ORM\ManyToOne(targetEntity="Cinema_hall")
     */
    private $cinemaHallId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="time_start", type="datetime")
     */
    private $timeStart;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="time_end", type="datetime")
     */
    private $timeEnd;

    /**
     * @var \Time
     *
     * @ORM\Column(name="time_movie_start", type="time")
     */
    private $timeMovieStart;



    /**
     * @var \Time
     *
     * @ORM\Column(name="time_movie_end", type="time")
     */
    private $timeMovieEnd;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

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
     * @param \DateTime $timeStart
     *
     * @return Cinema_hall_has_Movie
     */
    public function setTimeStart($timeStart)
    {
        $date_now = date("Y-m-d");
        if ($date_now <= $timeStart)
            $this->timeStart = $timeStart;

        return $this;
    }

    /**
     * Get time
     *
     * @return \DateTime
     */
    public function getTimeStart()
    {
        return $this->timeStart;
    }

    /**
     * Set time
     *
     * @param \DateTime $timeEnd
     *
     * @return Cinema_hall_has_Movie
     */
    public function setTimeEnd($timeEnd)
    {
        $date_now = date("Y-m-d");

        if ($date_now <= $timeEnd && $this->timeStart < $timeEnd)
            $this->timeEnd = $timeEnd;

        return $this;
    }

    /**
     * Get time
     *
     * @return \DateTime
     */
    public function getTimeEnd()
    {
        return $this->timeEnd;
    }

    /**
     * @return \Time
     */
    public function getTimeMovieStart()
    {
        return $this->timeMovieStart;
    }

    /**
     * @param \Time $timeMovieStart
     */
    public function setTimeMovieStart($timeMovieStart)
    {
        $this->timeMovieStart = $timeMovieStart;
    }

    /**
     * @return \Time
     */
    public function getTimeMovieEnd()
    {
        return $this->timeMovieEnd;
    }

    /**
     * @param \Time $timeMovieEnd
     */
    public function setTimeMovieEnd($timeMovieEnd)
    {
        if ($this->timeMovieStart < $timeMovieEnd)
            $this->timeMovieEnd = $timeMovieEnd;
    }

}

