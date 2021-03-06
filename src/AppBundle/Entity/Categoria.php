<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Trascastro\UserBundle\Entity\User;

/**
 * Categoria
 *
 * @ORM\Table(name="categoria")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CategoriaRepository")
 */
class Categoria
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
     * @ORM\Column(name="nombre", type="string", length=40, unique=true)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=255, unique=true)
     */
    private $descripcion;

    /**
     * @ORM\OneToMany(targetEntity="Trascastro\UserBundle\Entity\User", mappedBy="categoria" ,cascade={"persist", "refresh"})
     */

    private $usuarios;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Texto", mappedBy="categoria" ,cascade={"persist", "refresh"})
     */

    private $textos;

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
     * @return Categoria
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

    /**
     * Set descripcion
     *
     * @param string $descripcion
     *
     * @return Categoria
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    public function __toString()
    {
        return (string) $this->getNombre();
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->usuarios = new \Doctrine\Common\Collections\ArrayCollection();
        $this->textos = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add usuario
     *
     * @param \Trascastro\UserBundle\Entity\User $usuario
     *
     * @return Categoria
     */
    public function addUsuario(User $usuario)
    {
        $this->usuarios[] = $usuario;

        return $this;
    }

    /**
     * Remove usuario
     *
     * @param \Trascastro\UserBundle\Entity\User $usuario
     */
    public function removeUsuario(User $usuario)
    {
        $this->usuarios->removeElement($usuario);
    }

    /**
     * Get usuarios
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsuarios()
    {
        return $this->usuarios;
    }

    /**
     * Add texto
     *
     * @param Texto $texto
     *
     * @return Categoria
     */
    public function addTexto(Texto $texto)
    {
        $this->textos[] = $texto;

        return $this;
    }

    /**
     * Remove Texto
     *
     * @param Texto $texto
     */
    public function removeTexto(Texto $texto)
    {
        $this->usuarios->removeElement($texto);
    }

    /**
     * Get textos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTexto()
    {
        return $this->textos;
    }
}
