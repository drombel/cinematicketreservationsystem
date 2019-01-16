<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ticket_sold
 *
 * @ORM\Table(name="ticket_sold")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Ticket_soldRepository")
 */
class Ticket_sold
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
     * @ORM\Column(name="cinema_hall_numer", type="smallint")
     */
    private $cinemaHallNumer;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=255)
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="price", type="decimal", precision=8, scale=2)
     */
    private $price;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="time", type="datetime")
     */
    private $time;

    /**
     * @var array
     *
     * @ORM\Column(name="seat", type="array")
     */
    private $seat;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date")
     */
    private $date;

    /**
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="user")
     * @ORM\JoinColumn(name="userId", nullable = false)
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
     * Set cinemaHallNumer
     *
     * @param integer $cinemaHallNumer
     *
     * @return Ticket_sold
     */
    public function setCinemaHallNumer($cinemaHallNumer)
    {
        $this->cinemaHallNumer = $cinemaHallNumer;

        return $this;
    }

    /**
     * Get cinemaHallNumer
     *
     * @return int
     */
    public function getCinemaHallNumer()
    {
        return $this->cinemaHallNumer;
    }

    /**
     * Set city
     *
     * @param string $city
     *
     * @return Ticket_sold
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Ticket_sold
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set price
     *
     * @param string $price
     *
     * @return Ticket_sold
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set time
     *
     * @param \DateTime $time
     *
     * @return Ticket_sold
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

    /**
     * Set seat
     *
     * @param array $seat
     *
     * @return Ticket_sold
     */
    public function setSeat($seat)
    {
        $this->seat = $seat;

        return $this;
    }

    /**
     * Get seat
     *
     * @return array
     */
    public function getSeat()
    {
        return $this->seat;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     *
     * @return Ticket_sold
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
     * @return Ticket_sold
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
     * @param string $status
     *
     * @return Ticket_sold
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
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

}

