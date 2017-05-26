<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RevistaTexto
 *
 * @ORM\Table(name="revista_texto")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RevistaTextoRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class RevistaTexto
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
     * @var bool
     *
     * @ORM\Column(name="visto", type="boolean")
     */
    private $visto;

    /**
     * @var bool
     *
     * @ORM\Column(name="fav", type="boolean")
     */
    private $fav;

    /**
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Revista", inversedBy="revistaTexto")
     */
    private $revista;

    /**
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Texto", inversedBy="revistaTexto")
     */
    private $texto;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return RevistaTexto
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }


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
     * Set visto
     *
     * @param boolean $visto
     *
     * @return RevistaTexto
     */
    public function setVisto($visto)
    {
        $this->visto = $visto;

        return $this;
    }

    /**
     * Get visto
     *
     * @return bool
     */
    public function getVisto()
    {
        return $this->visto;
    }

    /**
     * Set fav
     *
     * @param boolean $fav
     *
     * @return RevistaTexto
     */
    public function setFav($fav)
    {
        $this->fav = $fav;

        return $this;
    }

    /**
     * Get fav
     *
     * @return bool
     */
    public function getFav()
    {
        return $this->fav;
    }

    public function setTexto(Texto $texto)
    {
        $this->texto = $texto;
        return $this;
    }

    public function getTexto()
    {
        return $this->texto;
    }

    public function setRevista(Revista $revista)
    {
        $this->revista = $revista;
        return $this;
    }

    public function getRevista()
    {
        return $this->revista;
    }



    public function __construct()
    {
        $this->setFav(false);
        $this->setVisto(false);
        $this->createdAt = new \DateTime();

    }
}

