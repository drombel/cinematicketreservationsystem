<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Movie
 *
 * @ORM\Table(name="movie")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MovieRepository")
 */
class Movie
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
     * @ORM\Column(name="title", type="string", length=100)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="poster", type="string", length=255, nullable = true)
     * @Assert\Image(
     *     minWidth = 270,
     *     maxWidth = 1350,
     *     minHeight = 400,
     *     maxHeight = 2000,
     *     minRatio = 0.6,
     *     maxRatio = 0.8
     * )
     */
    private $poster;

    /**
     * @var string
     *
     * @ORM\Column(name="scene", type="string", length=255, nullable = true)
     * @Assert\Image(
     *     minWidth = 1600,
     *     maxWidth = 3360,
     *     minHeight = 900,
     *     maxHeight = 2100,
     *     minRatio = 1.5,
     *     maxRatio = 1.65
     * )
     */
    private $scene;

    /**
     * @var string
     *
     * @ORM\Column(name="price", type="decimal", precision=8, scale=2)
     */
    private $price;


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
     * Set title
     *
     * @param string $title
     *
     * @return Movie
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
     * Set description
     *
     * @param string $description
     *
     * @return Movie
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getPoster()
    {
        return $this->poster;
    }

    /**
     * @param string $poster
     */
    public function setPoster($poster)
    {
        $this->poster = $poster;
    }

    /**
     * @return string
     */
    public function getScene()
    {
        return $this->scene;
    }

    /**
     * @param string $scene
     */
    public function setScene($scene)
    {
        $this->scene = $scene;
    }

    /**
     * Set price
     *
     * @param string $price
     *
     * @return Movie
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
     * @return mixed
     */

}

