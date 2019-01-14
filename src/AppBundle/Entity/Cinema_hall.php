<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * cinema_hall
 *
 * @ORM\Table(name="Cinema_hall")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Cinema_hallRepository")
 */
class Cinema_hall
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
     * @ORM\Column(name="number", type="smallint")
     */
    private $number;

    /**
     * @ORM\ManyToOne(targetEntity="Cinema")
     * @ORM\JoinColumn(name="cinemaId", nullable = false)
     */
    private $cinemaId;


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
     * Set number
     *
     * @param integer $number
     *
     * @return cinema_hall
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return int
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set cinemaId
     *
     * @param string $cinemaId
     *
     * @return cinema_hall
     */
    public function setCinemaId($cinemaId)
    {
        $this->cinemaId = $cinemaId;

        return $this;
    }

    /**
     * Get cinemaId
     *
     * @return string
     */
    public function getCinemaId()
    {
        return $this->cinemaId;
    }
}

