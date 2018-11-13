<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Seat
 *
 * @ORM\Table(name="seat")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SeatRepository")
 */
class Seat
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
     * @var string
     *
     * @ORM\Column(name="row", type="string", length=1)
     */
    private $row;

    /**
     * @var int
     *
     * @ORM\Column(name="col", type="smallint")
     */
    private $col;

    /**
     * @ORM\ManyToOne(targetEntity="Cinema_hall")
     * @ORM\JoinColumn(name="cinema_hallId", nullable = false)
     */
    private $cinema_hallId;


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
     * Set row
     *
     * @param string $row
     *
     * @return Seat
     */
    public function setRow($row)
    {
        $this->row = $row;

        return $this;
    }

    /**
     * Get row
     *
     * @return string
     */
    public function getRow()
    {
        return $this->row;
    }

    /**
     * Set col
     *
     * @param integer $col
     *
     * @return Seat
     */
    public function setCol($col)
    {
        $this->col = $col;

        return $this;
    }

    /**
     * Get col
     *
     * @return int
     */
    public function getCol()
    {
        return $this->col;
    }

    /**
     * Set cinema_hallId
     *
     * @param string $cinema_hallId
     *
     * @return Seat
     */
    public function setcinema_hallId($cinema_hallId)
    {
        $this->cinema_hallId = $cinema_hallId;

        return $this;
    }

    /**
     * Get cinema_hallId
     *
     * @return string
     */
    public function getcinema_hallId()
    {
        return $this->cinema_hallId;
    }
}

