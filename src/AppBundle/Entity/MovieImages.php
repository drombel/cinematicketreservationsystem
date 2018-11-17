<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * MovieImages
 *
 * @ORM\Table(name="movie_images")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MovieImagesRepository")
 */
class MovieImages
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
     * @ORM\Column(name="orginal", type="string", length=255, nullable = true)
     * @Assert\Image(
     *     minWidth = 1900,
     *     maxWidth = 3360,
     *     minHeight = 1200,
     *     maxHeight = 2100,
     *     minRatio = 1.58,
     *     maxRatio = 1.6
     * )
     */
    private $orginal;

    /**
     * @var string
     *
     * @ORM\Column(name="extra", type="string", length=255, nullable = true)
     * @Assert\Image(
     *     minWidth = 1900,
     *     maxWidth = 2560,
     *     minHeight = 1200,
     *     maxHeight = 1600,
     *     minRatio = 1.58,
     *     maxRatio = 1.6
     * )
     */
    private $extra;

    /**
     * @var string
     *
     * @ORM\Column(name="large", type="string", length=255, nullable = true)
     * @Assert\Image(
     *     minWidth = 1600,
     *     maxWidth = 1900,
     *     minHeight = 900,
     *     maxHeight = 1200,
     *     minRatio = 1.77,
     *     maxRatio = 1.8
     * )
     */
    private $large;

    /**
     * @var string
     *
     * @ORM\Column(name="normal", type="string", length=255, nullable = true)
     * @Assert\Image(
     *     minWidth = 1366,
     *     maxWidth = 1600,
     *     minHeight = 768,
     *     maxHeight = 900,
     *     minRatio = 1.77,
     *     maxRatio = 1.8
     * )
     */
    private $normal;

    /**
     * @var string
     *
     * @ORM\Column(name="small", type="string", length=255, nullable = true)
     * @Assert\Image(
     *     minWidth = 768,
     *     maxWidth = 1366,
     *     minHeight = 432,
     *     maxHeight = 768,
     *     minRatio = 1.77,
     *     maxRatio = 1.8
     * )
     */
    private $small;


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
     * Set orginal
     *
     * @param string $orginal
     *
     * @return MovieImages
     */
    public function setOrginal($orginal)
    {
        $this->orginal = $orginal;

        return $this;
    }

    /**
     * Get orginal
     *
     * @return string
     */
    public function getOrginal()
    {
        return $this->orginal;
    }

    /**
     * Set extra
     *
     * @param string $extra
     *
     * @return MovieImages
     */
    public function setExtra($extra)
    {
        $this->extra = $extra;

        return $this;
    }

    /**
     * Get extra
     *
     * @return string
     */
    public function getExtra()
    {
        return $this->extra;
    }

    /**
     * Set large
     *
     * @param string $large
     *
     * @return MovieImages
     */
    public function setLarge($large)
    {
        $this->large = $large;

        return $this;
    }

    /**
     * Get large
     *
     * @return string
     */
    public function getLarge()
    {
        return $this->large;
    }

    /**
     * Set normal
     *
     * @param string $normal
     *
     * @return MovieImages
     */
    public function setNormal($normal)
    {
        $this->normal = $normal;

        return $this;
    }

    /**
     * Get normal
     *
     * @return string
     */
    public function getNormal()
    {
        return $this->normal;
    }

    /**
     * Set small
     *
     * @param string $small
     *
     * @return MovieImages
     */
    public function setSmall($small)
    {
        $this->small = $small;

        return $this;
    }

    /**
     * Get small
     *
     * @return string
     */
    public function getSmall()
    {
        return $this->small;
    }

}

