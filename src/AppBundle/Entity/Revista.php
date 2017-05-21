<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Trascastro\UserBundle\Entity\User;

/**
 * Revista
 *
 * @ORM\Table(name="revista")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RevistaRepository")
 */
class Revista
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
     * @ORM\Column(name="nombre", type="string", length=255)
     */
    private $nombre;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\RevistaTexto", mappedBy="revista", cascade={"all"})
     */

    private $revistaTexto;


    /**
     * @ORM\OneToOne(targetEntity="Trascastro\UserBundle\Entity\User", inversedBy="revista", cascade={"all"})
     */
    private $dueño;

    public function setDueño(User $dueño)
    {
        $this->dueño = $dueño;
    }

    public function getDueño()
    {
        return $this->dueño;
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
     * Set nombre
     *
     * @param string $nombre
     *
     * @return Revista
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;

    }


    public function __construct()
    {
        $this->nombre = $this->getDueño(). "s Revista";
    }
}

