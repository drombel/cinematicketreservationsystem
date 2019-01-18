<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ticket
 *
 * @ORM\Table(name="ticket")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TicketRepository")
 */
class Ticket
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
     * @ORM\ManyToOne(targetEntity="Cinema_hall_has_Movie")
     * @ORM\JoinColumn(name="$cinemaHallHasMovieId", nullable=false)
     */
    private $cinemaHallHasMovieId;

    /**
     * @var array
     *
     * @ORM\Column(name="seatId", type="array")
     */
    private $seatId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date")
     */
    private $date;

    /**
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="userId", nullable = true)
     */
    private $userId;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @var array
     *
     * @ORM\Column(name="status", type="string", length=50, columnDefinition="ENUM('Canceled', 'Pending', 'Ok')")
     */
    private $status;


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
     * Set cinemaHallHasMovieId
     *
     * @param integer $cinemaHallHasMovieId
     *
     * @return Ticket
     */
    public function setCinemaHallHasMovieId($cinemaHallHasMovieId)
    {
        $this->cinemaHallHasMovieId = $cinemaHallHasMovieId;

        return $this;
    }

    /**
     * Get cinemaHallHasMovieId
     *
     * @return int
     */
    public function getCinemaHallHasMovieId()
    {
        return $this->cinemaHallHasMovieId;
    }

    /**
     * Set seatId
     *
     * @param array $seatId
     *
     * @return Ticket
     */
    public function setSeatId($seatId)
    {
        $this->seatId = $seatId;

        return $this;
    }

    /**
     * Get seatId
     *
     * @return array
     */
    public function getSeatId()
    {
        return $this->seatId;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     *
     * @return Ticket
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Ticket
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set status
     *
     * @param array $status
     *
     * @return Ticket
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return array
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    public function __construct()
    {
        $this->date = new \DateTime();
    }
}

